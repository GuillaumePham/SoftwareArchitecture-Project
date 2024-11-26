<?php

declare(strict_types=1);

namespace App\VO;

class Uid {
	private string $value;

	public function __construct(string $value) {
		$this->value = $value;
	}

	public function getValue(): string {
		return $this->value;
	}
	public function setValue(string $value): void {
		$this->value = $value;
	}

	public function __toString(): string {
		return $this->value;
	}
}