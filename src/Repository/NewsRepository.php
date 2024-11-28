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
		$result = $this->dbAdapter->query("news", ['id' => $id]);
		if (!$result) {
			return null;
		}

		$row = $result[0];
		return $this->tryHydrateNews($row);
	}

	public function findAll(): ?array {
		$results = $this->dbAdapter->query("news");
		if (!$results) {
			return null;
		}

		return $this->tryHydrateNewsList($results);
	}
}
