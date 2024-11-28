<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\News;
use App\Adapter\DbAdapter;
use App\VO\Uid;
use DateTimeImmutable;

class NewsRepository extends Repository {
	public function __construct(
		protected DbAdapter $dbAdapter
	) { }

	protected function getTableName(): string {
		return 'news';
	}

	public function findById(Uid $id): ?News {
		$result = $this->dbAdapter->query("news", ['id' => $id]);
		if (!$result) {
			return null;
		}

		$row = $result[0];
		return News::tryHydrateNews($row);
	}

	public function findAll(): ?array {
		$results = $this->dbAdapter->query("news");
		if (!$results) {
			return null;
		}

		return News::tryHydrateNewsList($results);
	}
}
