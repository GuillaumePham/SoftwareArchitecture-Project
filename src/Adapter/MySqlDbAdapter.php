<?php

declare(strict_types=1);

namespace App\Adapter;

use PDO;
use App\VO\Uid;

class MySqlDbAdapter implements DbAdapter {
	private $pdo;

	public function __construct(
		string $host,
		string $db,
		string $user,
		string $password
	) {
		$dsn = "mysql:host=$host;dbname=$db";
		$this->pdo = new PDO($dsn, $user, $password);
	}

	private function execute(string $sql, array $params = []): bool {
		$statement = $this->pdo->prepare($sql);
		return $statement->execute($params);
	}

	public function query(string $tableName, array $where = []): array|false {
		$sql = "SELECT * FROM $tableName";
		if (!empty($where)) {
			$conditions = [];
			foreach ($where as $field => $value) {
				$conditions[] = "$field = :$field";
			}
			$conditions = implode(' AND ', $conditions);
			$sql .= " WHERE $conditions";
		}
		$statement = $this->pdo->prepare($sql);
		$statement->execute($where);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}


	public function createEntity(Uid $id, string $tableName, array $data): bool {
		$data['id'] = $id;

		$fields = array_keys($data);
		$formattedFieldDefs = implode(', ', $fields);
		$formattedFieldValues = implode(
			', ',
			array_map(fn($field) => ":$field", $fields)
		);

		$sql = "INSERT INTO $tableName ($formattedFieldDefs)
				VALUES ($formattedFieldValues)";

		return $this->execute($sql, $data);
	}

	public function updateEntity(Uid $id, string $tableName, array $data): bool {
		$fields = array_keys($data);
		$formattedFields = implode(
			', ',
			array_map(fn($field) => "$field = :$field", $fields)
		);

		$sql = "UPDATE $tableName
				SET $formattedFields
				WHERE id = :id";
		$data['id'] = $id;
		return $this->execute($sql, $data);
	}


	public function deleteEntity(Uid $id, string $tableName): bool {
		$sql = "DELETE FROM $tableName WHERE id = :id";
		return $this->execute($sql, ['id' => $id]);
	}

	public function addTable(string $tableName, array $schema): void {
		$fields = [];
		foreach ($schema as $fieldName => $fieldType) {
			$fields[] = "$fieldName $fieldType";
		}
		$formattedFields = implode(', ', $fields);

		$sql = "CREATE TABLE IF NOT EXISTS $tableName (
			$formattedFields
		)";
		$this->execute($sql);
	}
	public function clearTable(string $tableName): void {
		$sql = "DELETE FROM $tableName";
		$this->execute($sql);
	}
	public function clearAll(): void {
		$tables = $this->query("SHOW TABLES");
		foreach ($tables as $table) {
			$table = array_values($table)[0];
			$this->clearTable($table);
		}
	}

}