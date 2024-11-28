<?php

declare(strict_types=1);

namespace App\Repository;

use App\Adapter\DbAdapter;

abstract class Repository {
	protected DbAdapter $dbAdapter;

	public function __construct(DbAdapter $dbAdapter) {
		$this->dbAdapter = $dbAdapter;
	}

	abstract protected function getTableName(): string;
}
