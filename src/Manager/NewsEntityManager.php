<?php

declare(strict_types=1);

namespace App\Manager;

use App\Adapter\IDbAdapter;
use App\Repository\NewsRepository;
use App\Model\News;
use App\Model\User;
use App\VO\Uid;
use App\VO\Event;

class NewsEntityManager {
	private NewsRepository $newsRepository;
	private Event $createNewsEvent;
	public function getCreateNewsEvent(): Event { return $this->createNewsEvent; }
	private Event $createUserNewsEvent;
	public function getCreateUserNewsEvent(): Event { return $this->createUserNewsEvent; }
	private Event $updateNewsEvent;
	public function getUpdateNewsEvent(): Event { return $this->updateNewsEvent; }
	private Event $deleteNewsEvent;
	public function getDeleteNewsEvent(): Event { return $this->deleteNewsEvent; }


	public function __construct(
		private IDbAdapter $dbAdapter,
	) {
		$this->newsRepository = new NewsRepository($this->dbAdapter);

		$this->createNewsEvent = new Event();
		$this->createUserNewsEvent = new Event();
		$this->updateNewsEvent = new Event();
		$this->deleteNewsEvent = new Event();


		$this->dbAdapter->addTable($this->newsRepository->getTableName(), [
			'id' => 'varchar(36) primary key',
			'content' => 'varchar(256) not null',
			'created_at' => 'datetime not null'
		]);
	}

	public function getDbAdapter(): IDbAdapter {
		return $this->dbAdapter;
	}

	public function getTableName(): string {
		return $this->newsRepository->getTableName();
	}

	public function getById(Uid $id): ?News {
		return $this->newsRepository->findById($id);
	}

	public function getAll(): ?array {
		return $this->newsRepository->findAll();
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
			$createdNews = $this->newsRepository->findById($news->getId());
			$this->createNewsEvent->dispatch($createdNews);
			return $createdNews;
		}
	}
	public function createUserNews(Uid $id, User $user): ?News {
		$currentTime = new \DateTime();
		$data = [
			'content' => "User {$user->getLogin()} just joined the site.",
			'created_at' => $currentTime->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->createEntity(
			$id,
			$this->newsRepository->getTableName(),
			$data
		)) {
			$createdNews = $this->newsRepository->findById($id);
			$this->createUserNewsEvent->dispatch($createdNews, $user);
			return $createdNews;
		}
	}

	public function update(Uid $id, ?string $content): ?News {
		$data = [];
		if ($content !== null) { $data['content'] = $content; }

		if ($this->dbAdapter->updateEntity(
			$id,
			$this->newsRepository->getTableName(),
			$data
		)) {
			$updatedNews = $this->newsRepository->findById($id);
			$this->updateNewsEvent->dispatch($updatedNews);
			return $updatedNews;
		}
	}

	public function delete(Uid $id): bool {
		if ($this->dbAdapter->deleteEntity(
			$id,
			$this->newsRepository->getTableName()
		)) {
			$this->deleteNewsEvent->dispatch($id);
			return true;
		}
		return false;
	}
}