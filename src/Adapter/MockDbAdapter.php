<?php

declare(strict_types=1);

namespace App\Adapter;

class MockDbAdapter implements IDbAdapter {
	private $data;

	public function __construct() {
		$this->data = [
			'users' => [
				['id' => 1, 'name' => 'Axouille', 'email' => 'chad@ricardo.com'],
				['id' => 2, 'name' => 'Guigui', 'email' => 'feur@gmail.com'],
			]
		];
	}
	public function query(string $sql, array $params = []): array|false {
		$table = $this->getTableNameFromQuery($sql);
		return $this->data[$table] ?? [];
	}

	public function execute(string $sql, array $params = []): void {
		$table = $this->getTableNameFromQuery($sql);
		if (!isset($this->data[$table])) {
			$this->data[$table] = [];
		}
		$this->data[$table][] = $params;
	}

	private function getTableNameFromQuery(string $sql): string {
		preg_match('/from\s+(\w+)/i', $sql, $matches);
		return $matches[1] ?? '';
	}
}