<?php

declare(strict_types=1);

namespace App\Manager;

use App\Adapter\IDbAdapter;

use App\Repository\UserRepository;
use App\Model\User;
use App\VO\Uid;
use App\VO\Event;

class UserEntityManager {
	private UserRepository $userRepository;
	private Event $createUserEvent;
	public function getCreateUserEvent(): Event { return $this->createUserEvent; }
	private Event $updateUserEvent;
	public function getUpdateUserEvent(): Event { return $this->updateUserEvent; }
	private Event $deleteUserEvent;
	public function getDeleteUserEvent(): Event { return $this->deleteUserEvent; }

	public function __construct(
		private IDbAdapter $dbAdapter
	) {
		$this->userRepository = new UserRepository($this->dbAdapter);

		$this->createUserEvent = new Event();
		$this->updateUserEvent = new Event();
		$this->deleteUserEvent = new Event();

		$this->dbAdapter->addTable($this->userRepository->getTableName(), [
			'id' => 'varchar(36) primary key',
			'login' => 'varchar(256) not null',
			'password' => 'varchar(256) not null',
			'email' => 'varchar(256) not null',
			'created_at' => 'datetime not null'
		]);
	}

	public function getDbAdapter(): IDbAdapter {
		return $this->dbAdapter;
	}

	public function getTableName(): string {
		return $this->userRepository->getTableName();
	}

	public function getById(Uid $id): ?User {
		return $this->userRepository->findById($id);
	}

	public function getAll(): ?array {
		return $this->userRepository->findAll();
	}

	public function create(User $user): ?User {
		$data = [
			'login' => $user->getLogin(),
			'password' => $user->getPassword(),
			'email' => $user->getEmail(),
			'created_at' => $user->getCreatedAt()->format(\DateTime::ATOM)
		];
		if ($this->dbAdapter->createEntity(
			$user->getId(),
			$this->userRepository->getTableName(),
			$data
		)) {
			$createdUser = $this->userRepository->findById($user->getId());
			$this->createUserEvent->dispatch($createdUser);
			return $createdUser;
		}
		return null;
	}

	public function update(Uid $id, ?string $login = null, ?string $password = null, ?string $email = null): ?User {
		$data = [];
		if ($login !== null) { $data['login'] = $login; }
		if ($password !== null) { $data['password'] = $password; }
		if ($email !== null) { $data['email'] = $email; }

		if ($this->dbAdapter->updateEntity(
			$id,
			$this->userRepository->getTableName(),
			$data
		)) {
			$updatedUser = $this->userRepository->findById($id);
			$this->updateUserEvent->dispatch($updatedUser);
			return $updatedUser;
		}
	}

	public function delete(Uid $id): bool {
		if ($this->dbAdapter->deleteEntity(
			$id,
			$this->userRepository->getTableName()
		)) {
			$this->deleteUserEvent->dispatch($id);
			return true;
		}
		return false;
	}
}
