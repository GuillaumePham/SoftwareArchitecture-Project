<?php

declare(strict_types=1);

namespace App\Adapter;

use PDO;

class MySqlDbAdapter implements IDbAdapter {
	private $pdo;

	public function __construct(
		string $host,
		string $db,
		string $user,
		string $password
	) {
		$dsn = "mysql:host=$host;dbname=$db";
		$this->pdo = new PDO($dsn, $user, $password);

		$this->pdo->exec("
			create table if not exists news (
				id varchar(36) primary key,
				content varchar(256) not null,
				created_at datetime not null
			)
		");
	}

	public function query(string $sql, array $params = []): array|false {
		$statement = $this->pdo->prepare($sql);
		$operation = $statement->execute($params);
		var_dump($operation);

		// return the result
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		var_dump($result);
		return $result;
	}

	public function execute(string $sql, array $params = []): void {
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);
	}

}