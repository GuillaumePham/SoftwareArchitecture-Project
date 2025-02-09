<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;
use App\Adapter\IDbAdapter;
use App\VO\Uid;

class UserRepository extends Repository {
	private static string $tableName = 'user';

	public function __construct(
		protected IDbAdapter $dbAdapter
	) { }

	public function getTableName(): string {
		return self::$tableName;
	}

	public function findById(Uid $id): ?User {
		$result = $this->dbAdapter->query($this->getTableName(), ['id' => $id]);
		if (!$result) {
			return null;
		}

		$row = $result[0];
		return User::tryHydrateUser($row);
	}

	public function findAll(): ?array {
		$results = $this->dbAdapter->query($this->getTableName());
		if (!$results) {
			return null;
		}

		return User::tryHydrateUserList($results);
	}
}
