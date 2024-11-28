<?php

declare(strict_types=1);

namespace App;

use App\Adapter\IDbAdapter;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

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
		$this->newsRepository = new NewsRepository($this->dbAdapter);
	}

	public function getById(Uid $id): ?News {
		return $this->newsRepository->findById($id);
	}


	private function createEntity(Uid $id, string $tableName, array $data): bool {
		$data['id'] = $id;

		$fields = array_keys($data);
		$formattedFieldDefs = implode(', ', $fields);
		$formattedFieldValues = implode(
			', ',
			array_map(fn($field) => ":$field", $fields)
		);

		$sql = "INSERT INTO $tableName ($formattedFieldDefs)
				VALUES ($formattedFieldValues)";

		return $this->dbAdapter->execute($sql, $data);
	}

	private function updateEntity(Uid $id, string $tableName, array $data): bool {
		$fields = array_keys($data);
		$formattedFields = implode(
			', ',
			array_map(fn($field) => "$field = :$field", $fields)
		);

		$sql = "UPDATE $tableName
				SET $formattedFields
				WHERE id = :id";
		$data['id'] = $id;
		return $this->dbAdapter->execute($sql, $data);
	}


	private function deleteEntity(Uid $id, string $tableName): bool {
		$sql = "DELETE FROM $tableName WHERE id = :id";
		return $this->dbAdapter->execute($sql, ['id' => $id]);
	}


	public function create(News $news): ?News {
		$data = [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->createEntity($news->getId(), "news", $data)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function update(News $news): ?News {
		$data = [
			'content' => $news->getContent(),
			'created_at' => $news->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->updateEntity($news->getId(), "news", $data)) {
			return $this->newsRepository->findById($news->getId());
		}
	}

	public function delete(Uid $id): void {
		$this->deleteEntity($id, "news");
	}

	public function clear(): void {
		$sql = 'DELETE FROM news';
		$this->dbAdapter->execute($sql);
	}

}