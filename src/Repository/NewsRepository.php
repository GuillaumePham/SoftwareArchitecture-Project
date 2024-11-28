<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\News;
use App\Adapter\IDbAdapter;
use App\VO\Uid;

class NewsRepository extends Repository {
	private static string $tableName = 'news';

	public function __construct(
		protected IDbAdapter $dbAdapter
	) { }

	public function getTableName(): string {
		return self::$tableName;
	}

	public function findById(Uid $id): ?News {
		$result = $this->dbAdapter->query($this->getTableName(), ['id' => $id]);
		if (!$result) {
			return null;
		}

		$row = $result[0];
		return News::tryHydrateNews($row);
	}

	public function findAll(): ?array {
		$results = $this->dbAdapter->query($this->getTableName());
		if (!$results) {
			return null;
		}

		return News::tryHydrateNewsList($results);
	}
}
