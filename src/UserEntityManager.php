<?php

declare(strict_types=1);

namespace App;

use App\Adapter\IDbAdapter;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use App\Exception\UserNotFoundException;
use App\Exception\DbException;
use App\Adapter\MockDbAdapter;
use App\Adapter\MySqlDbAdapter;
use App\Repository\UserRepository;
use App\Model\User;
use App\VO\Uid;

class UserEntityManager {
	private IDbAdapter $dbAdapter;
	private UserRepository $userRepository;

	public function __construct() {
		$config = parse_ini_file('config.ini');

		$this->dbAdapter = new MySqlDbAdapter(
			host: $config['host'],
			db: $config['db'],
			user: $config['user'],
			password: $config['password']
		);
		// $this->dbAdapter = new MockDbAdapter();

		$this->userRepository = new UserRepository($this->dbAdapter);


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

	public function getById(Uid $id): ?User {
		return $this->userRepository->findById($id);
	}


	public function add(User $user): ?User {
		try {
			$data = [
				'login' => $user->getLogin()
				'password' => $user->getPassword()
				'login' => $user->getLogin()
				'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
			];
			if ($this->dbAdapter->createEntity(
				$user->getId(),
				$this->userRepository->getTableName(),
				$data
			)) {
				return $this->userRepository->findById($user->getId());
			}
		} catch (DbException $e) {
			 error_log($e->getMessage());
			 throw new RepositoryException("Error adding user", 0, $e);
		}
	}

	public function update(User $user): ?User {
		try {
			$data = [
				'login' => $user->getLogin()
				'password' => $user->getPassword()
				'login' => $user->getLogin()
				'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
			];
			if ($this->dbAdapter->updateEntity(
				$user->getId(),
				$this->userRepository->getTableName(),
				$data
			)) {
				return $this->userRepository->findById($user->getId());
			}

		} catch (DbException $e) {
			 error_log($e->getMessage());
			 throw new RepositoryException("Error updating user", 0, $e);
		}
	}

	public function delete(Uid $id): void {
		try {
			$this->dbAdapter->deleteEntity($id, $this->userRepository->getTableName());
		} catch (DbException $e) {
			  error_log($e->getMessage());
			  throw new RepositoryException("Error deleting user", 0, $e);
		}
	}
}