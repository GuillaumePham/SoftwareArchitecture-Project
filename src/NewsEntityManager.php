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
	private IDbAdapter $dbAdapter;
	private NewsRepository $newsRepository;

	public function __construct() {
		$config = parse_ini_file('config.ini');

		$this->dbAdapter = new MySqlDbAdapter(
			host: $config['host'],
			db: $config['db'],
			user: $config['user'],
			password: $config['password']
		);
		// $this->dbAdapter = new MockDbAdapter();

		$this->dbAdapter->addTable("news", [
			'id' => 'varchar(36) primary key',
			'content' => 'varchar(256) not null',
			'created_at' => 'datetime not null'
		]);

		$this->newsRepository = new NewsRepository($this->dbAdapter);
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
		if ($this->dbAdapter->createEntity($news->getId(), "news", $data)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function update(News $news): ?News {
		$data = [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->updateEntity($news->getId(), "news", $data)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function delete(Uid $id): void {
		$this->dbAdapter->deleteEntity($id, "news");
	}
}