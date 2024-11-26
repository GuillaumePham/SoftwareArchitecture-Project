<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\News;
use App\Adapter\IDbAdapter;
use App\VO\Uid;
use DateTimeImmutable;

class NewsRepository extends Repository {
	public function __construct(
		protected IDbAdapter $dbAdapter
	) { }

	protected function getTableName(): string {
		return 'news';
	}

	private function hydrateNews(array $row): News {
		if (!isset($row['id'])) {
			throw new \InvalidArgumentException('Missing id in row');
		}
		if (!isset($row['content'])) {
			throw new \InvalidArgumentException('Missing content in row');
		}
		if (!isset($row['created_at'])) {
			throw new \InvalidArgumentException('Missing created_at in row');
		}

		return new News(
			new Uid($row['id']),
			$row['content'],
			new DateTimeImmutable($row['created_at'])
		);
	}
	private function tryHydrateNews(array $row): ?News {
		try {
			return $this->hydrateNews($row);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
	}

	private function hydrateNewsList(array $rows): array {
		$news = [];
		foreach ($rows as $row) {
			$news[] = $this->hydrateNews($row);
		}
		return $news;
	}
	private function tryHydrateNewsList(array $rows): array {
		$news = [];
		foreach ($rows as $row) {
			$row = $this->tryHydrateNews($row);
			if ($row !== null) {
				$news[] = $row;
			}
		}
		return $news;
	}


	public function findById(Uid $id): ?News {
		$sql = 'SELECT * FROM news
				WHERE id = :id';
		$result = $this->dbAdapter->query($sql, ['id' => $id]);
		if (!$result) {
			return null;
		}

		$row = $result[0];
		return $this->tryHydrateNews($row);
	}

	public function findAll(): ?array {
		$sql = 'SELECT * FROM news';
		$results = $this->dbAdapter->query($sql);
		if (!$results) {
			return null;
		}

		return $this->tryHydrateNewsList($results);
	}

	public function createNews(News $news): bool {
		$sql = 'INSERT INTO news (id, content, created_at)
				VALUES (:id, :content, :created_at)';
		return $this->dbAdapter->execute($sql, [
			'id' => $news->getId(),
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		]);
	}

	public function updateNews(News $news): bool {
		$sql = 'UPDATE news
				SET content = :content, created_at = :created_at
				WHERE id = :id';
		return $this->dbAdapter->execute($sql, [
			'id' => $news->getId(),
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		]);
	}

	public function deleteNews(Uid $id): bool {
		$sql = 'DELETE FROM news
				WHERE id = :id';
		return $this->dbAdapter->execute($sql, ['id' => $id]);
	}

	public function clear(): void {
		$sql = 'DELETE FROM news';
		$this->dbAdapter->execute($sql);
	}
}
