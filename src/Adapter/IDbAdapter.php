<?php

declare(strict_types=1);

namespace App\Adapter;
use App\VO\Uid;

interface IDbAdapter {
	public function query(string $tableName, array $where = []): array|bool;


	public function createEntity(Uid $id, string $tableName, array $data): bool;
	public function updateEntity(Uid $id, string $tableName, array $data): bool;
	public function deleteEntity(Uid $id, string $tableName): bool;

	public function addTable(string $tableName, array $schema): void;
	public function clearTable(string $tableName): void;
}
