<?php

declare(strict_types=1);

namespace App\Service;

use App\Adapter\IDbAdapter;
use App\Model\News;

class NewsService {
	public function __construct(
		private IDbAdapter $dbAdapter
	) { }

	public function createNews(News $news): bool {
		$sql = 'INSERT INTO news (title, content, created_at) VALUES (:title, :content, :created_at)';
		$result = $this->dbAdapter->query($sql, [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		]);
		var_dump($result);
		return true;
	}

	public function updateNews(News $news): bool {
		$sql = 'UPDATE news SET title = :title, content = :content WHERE id = :id';
		$result = $this->dbAdapter->query($sql, [
			'id' => $news->getId(),
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		]);
		var_dump($result);
		return true;
	}

	public function deleteNews(int $id): bool {
		$sql = 'DELETE FROM news WHERE id = :id';
		$result = $this->dbAdapter->query($sql, ['id' => $id]);
		var_dump($result);
		return true;
	}
}