<?php

declare(strict_types=1);

namespace App\Repository;

use App\Adapter\IDbAdapter;

abstract class Repository {
	protected IDbAdapter $dbAdapter;

	public function __construct(IDbAdapter $dbAdapter) {
		$this->dbAdapter = $dbAdapter;
	}

	abstract public function getTableName(): string;
}
