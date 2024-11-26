<?php

declare(strict_types=1);

namespace App;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use App\Adapter\MySqlDbAdapter;
use App\Repository\NewsRepository;
use App\Model\News;
use App\VO\Uid;

class NewsEntityManager {
	private NewsRepository $newsRepository;

	public function __construct() {
		$config = parse_ini_file('config.ini');

		$dbAdapter = new MySqlDbAdapter(
			host: $config['host'],
			db: $config['db'],
			user: $config['user'],
			password: $config['password']
		);
		$this->newsRepository = new NewsRepository($dbAdapter);
	}

	public function getById(Uid $id): ?News {
		return $this->newsRepository->findById($id);
	}

	public function create(News $news): ?News {
		if ($this->newsRepository->createNews($news)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function update(News $news): ?News {
		if ($this->newsRepository->updateNews($news)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function delete(Uid $id): void {
		$this->newsRepository->deleteNews($id);
	}

	public function clear(): void {
		$this->newsRepository->clear();
	}

}