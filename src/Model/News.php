<?php

declare(strict_types=1);

namespace App\Model;

use App\VO\Uid;

use DateTimeInterface;

class News implements Model {
	private Uid $id;
	private string $content;
	private DateTimeInterface $created_at;

	public function __construct(
		Uid $id,
		string $content,
		DateTimeInterface $created_at
	) {
		$this->id = $id;
		$this->content = $content;
		$this->created_at = $created_at;
	}

	public function getId(): Uid {
		return $this->id;
	}
	public function setId(Uid $id): void {
		$this->id = $id;
	}

	public function getContent(): string {
		return $this->content;
	}
	public function setContent(string $content): void {
		$this->content = $content;
	}

	public function getCreatedAt(): DateTimeInterface {
		return $this->created_at;
	}
	public function setCreatedAt(DateTimeInterface $date): void {
		$this->created_at = $date;
	}

	public function __tostring(): string {
		return sprintf(
			'[%s] %s',
			$this->created_at->format('Y-m-d H:i:s'),
			$this->content
		);
	}
}