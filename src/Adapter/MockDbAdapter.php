<?php

declare(strict_types=1);

namespace App\Adapter;

use App\VO\Uid;

class MockDbAdapter implements DbAdapter {
	private $data = [];

	public function __construct() {}

	public function query(string $tableName, array $where = []): array|bool {
		if (!isset($this->data[$tableName])) {
			return false;
		}

		if (empty($where)) {
			return $this->data[$tableName];
		}

		$results = [];
		foreach (array_keys($this->data[$tableName]) as $id) {
			$rowData = $this->data[$tableName][$id];
			$row = ['id' => (string)$id] + $rowData;

			if (array_intersect_assoc($where, $row) === $where) {
				$results[] = $row;
			}
		}
		return $results;
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