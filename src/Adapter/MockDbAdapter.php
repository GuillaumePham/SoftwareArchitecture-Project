<?php

declare(strict_types=1);

namespace App\Adapter;

use App\VO\Uid;

class MockDbAdapter implements IDbAdapter {
	private $data = [];

	public function __construct() {}

	public function query(string $tableName, array $where = []): array|bool {
		if (empty($where)) {
			return $this->data[$tableName];
		}

		$result = [];
		foreach ($this->data[$tableName] as $row) {
			$match = true;
			foreach ($where as $field => $value) {
				if (!isset($row[$field]) || $row[$field] !== $value) {
					$match = false;
					break;
				}
			}
			if ($match) {
				$result[] = $row;
			}
		}
		return $result;
	}

	public function addTable(string $tableName, array $schema): void {
		if (!isset($this->data[$tableName])) {
			$this->data[$tableName] = [];
		}
	}

	public function clearAll(): void {
		$this->data = [];
	}

	public function clearTable(string $tableName): void {
		$this->data[$tableName] = [];
	}

	public function createEntity(Uid $id, string $tableName, array $data): bool {
		$this->data[$tableName][$id->getValue()] = $data;
		return true;
	}

	public function deleteEntity(Uid $id, string $tableName): bool {
		unset($this->data[$tableName][$id->getValue()]);
		return true;
	}
	public function updateEntity(Uid $id, string $tableName, array $data): bool {
		$this->data[$tableName][$id->getValue()] = $data;
		return true;
	}
}