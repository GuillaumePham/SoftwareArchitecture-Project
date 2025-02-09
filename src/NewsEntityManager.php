<?php

declare(strict_types=1);

namespace App;

use App\Adapter\IDbAdapter;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use App\Adapter\MockDbAdapter;
use App\Adapter\MySqlDbAdapter;
use App\Repository\NewsRepository;
use App\Model\News;
use App\VO\Uid;

class NewsEntityManager {
	private NewsRepository $newsRepository;

	public function __construct(
		private IDbAdapter $dbAdapter,
	) {
		$this->newsRepository = new NewsRepository($this->dbAdapter);


		$this->dbAdapter->addTable($this->newsRepository->getTableName(), [
			'id' => 'varchar(36) primary key',
			'content' => 'varchar(256) not null',
			'created_at' => 'datetime not null'
		]);
	}

	public function getDbAdapter(): IDbAdapter {
		return $this->dbAdapter;
	}

	public function getById(Uid $id): ?News {
		return $this->newsRepository->findById($id);
	}


	public function create(News $news): ?News {
		$data = [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->createEntity(
			$news->getId(),
			$this->newsRepository->getTableName(),
			$data
		)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function update(News $news): ?News {
		$data = [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->updateEntity(
			$news->getId(),
			$this->newsRepository->getTableName(),
			$data
		)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function delete(Uid $id): void {
		$this->dbAdapter->deleteEntity($id, $this->newsRepository->getTableName());
	}
}