<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\UserNotFoundException;
use App\Exception\DbException;
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

    public function add(User $user): void {
        try {
            $data = [
                'id' => $user->getId()->getValue(),
                'login' => $user->getLogin(),
                'password' => $user->getPassword(),
                'email' => $user->getEmail(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
            $this->dbAdapter->createEntity($this->getTableName(), $data);
        } catch (DbException $e) {
            error_log($e->getMessage());
            throw new RepositoryException("Error adding user", 0, $e);
        }
    }

	public function update(User $user): void {
        try {
            $data = [
                'login' => $user->getLogin(),
                'password' => $user->getPassword(),
                'email' => $user->getEmail(),
            ];
            $where = ['id' => $user->getId()->getValue()];

            $this->dbAdapter->updateEntity($where, $this->getTableName(), $data);
        } catch (DbException $e) {
            error_log($e->getMessage());
            throw new RepositoryException("Error updating user", 0, $e);
        }
    }

	public function delete(User $user): void {
        try {
            $where = ['id' => $user->getId()->getValue()];

            $this->dbAdapter->deleteEntity($where, $this->getTableName());
        } catch (DbException $e) {
            error_log($e->getMessage());
            throw new RepositoryException("Error deleting user", 0, $e);
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
