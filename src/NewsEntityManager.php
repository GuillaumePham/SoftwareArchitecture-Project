<?php

declare(strict_types=1);

namespace App;

// require_once dirname(__FILE__) . '/vendor/autoload.php';

use App\Adapter\MySqlDbAdapter;
use App\Repository\NewsRepository;
use App\Model\News;
use App\VO\Uid;

class NewsEntityManager {
	private NewsRepository $newsRepository;
	// private NewsService $newsService;

	public function __construct() {
		$config = parse_ini_file('config.ini');

		$dbAdapter = new MySqlDbAdapter(
			host: $config['host'],
			db: $config['db'],
			user: $config['user'],
			password: $config['password']
		);
		$this->newsRepository = new NewsRepository($dbAdapter);
		// $this->newsService = new NewsService($dbAdapter);
	}

	public function getById(Uid $id): ?News {
		// $data = $this->newsRepository->findById($id);

		// return new News(
		// 	id: new Uid($data['id']),
		// 	content: $data['content'],
		// 	created_at: new DateTimeImmutable($data['created_at'])
		// );
		return $this->newsRepository->findById($id);
	}

	public function create(News $news): ?News {
		// $sql = 'INSERT INTO news (id, content, created_at) VALUES (:id, :content, :created_at)';
		// $params = [
		// 	'id' => $news->getId()->getValue(),
		// 	'content' => $news->getContent(),
		// 	'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		// ];
		// $this->newsService->createNews($sql, $params);

		// return $news;
		return $this->newsRepository->createNews($news);
	}

	public function update(News $news): ?News {
		return $this->newsRepository->updateNews($news);
	}

	public function delete(Uid $id): void {
		var_dump($this->newsRepository->deleteNews($id));
	}

	public function clear(): void {
		$this->newsRepository->clear();
	}

}