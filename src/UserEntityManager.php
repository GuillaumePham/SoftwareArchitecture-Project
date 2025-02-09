<?php

declare(strict_types=1);

namespace App;

use App\Adapter\IDbAdapter;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use App\Exception\RepositoryException;
use App\Exception\DbException;
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

	public function create(User $user): ?User {
		$data = [
			'login' => $user->getLogin(),
			'password' => $user->getPassword(),
			'email' => $user->getEmail(),
			'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->createEntity(
			$user->getId(),
			$this->userRepository->getTableName(),
			$data
		)) {
			$createdUser = $this->userRepository->findById($user->getId());
			//$this->sendEmail($user->getEmail(), "Account Created", "Your account has been successfully created.");
			return $createdUser;
		}
		return null;
	}

	public function update(User $user): ?User {
		$data = [
			'login' => $user->getLogin(),
			'password' => $user->getPassword(),
			'email' => $user->getEmail(),
			'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
		];
		if ($this->dbAdapter->updateEntity(
			$user->getId(),
			$this->userRepository->getTableName(),
			$data
		)) {
			$updatedUser = $this->userRepository->findById($user->getId());
			//$this->sendEmail($user->getEmail(), "Account Updated", "Your account details have been successfully updated.");
			return $updatedUser;
		}
	}

	public function delete(Uid $id): void {
		$this->dbAdapter->deleteEntity($id, $this->userRepository->getTableName());
	}

	private function sendEmail(string $to, string $subject, string $message): void {
		$headers = "From: noreply@example.com\r\n";
		$headers .= "Reply-To: noreply@example.com\r\n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

		if (!mail($to, $subject, $message, $headers)) {
			error_log("Failed to send email to $to");
		}
	}
}
