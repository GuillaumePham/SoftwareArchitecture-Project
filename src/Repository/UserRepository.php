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
        try {
            $stmt = $this->dbAdapter->prepare("SELECT * FROM " . $this->getTableName() . " WHERE id = :id");
            $stmt->execute(['id' => $id->getValue()]);
            $result = $stmt->fetch();

            if (!$result) {
                return null;
            }

            return User::tryHydrateUser($result);
        } catch (DbException $e) {
            error_log($e->getMessage());
            throw new RepositoryException("Error fetching user by ID", 0, $e);
        }
    }

    public function findAll(): array {
        try {
            $results = $this->dbAdapter->query($this->getTableName());
            return User::tryHydrateUserList($results) ?? [];
        } catch (DbException $e) {
            error_log($e->getMessage());
            throw new RepositoryException("Error fetching all users", 0, $e);
        }
    }
}
