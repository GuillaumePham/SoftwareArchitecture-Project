<?php

declare(strict_types=1);

namespace App\Adapter;

interface IDbAdapter {
	public function query(string $sql, array $params = []): array|bool;
	public function execute(string $sql, array $params = []): void;
}
