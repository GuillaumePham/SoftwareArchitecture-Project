<?php

declare(strict_types=1);

namespace App\Model;

use App\VO\Uid;

use DateTimeInterface;
use DateTimeImmutable;

class News implements Model {
	private Uid $id;
	private string $content;
	private DateTimeInterface $createdAt;

	public function __construct(
		Uid $id,
		string $content,
		DateTimeInterface $createdAt
	) {
		$this->id = $id;
		$this->content = $content;
		$this->createdAt = $createdAt;
	}

	public static function hydrateNews(array $row): News {
		if (!isset($row['id'])) {
			throw new \InvalidArgumentException('Missing id in row');
		}
		if (!isset($row['content'])) {
			throw new \InvalidArgumentException('Missing content in row');
		}
		if (!isset($row['created_at'])) {
			throw new \InvalidArgumentException('Missing created_at in row');
		}

		return new News(
			new Uid($row['id']),
			$row['content'],
			new DateTimeImmutable($row['created_at'])
		);
	}
	public static function tryHydrateNews(array $row): ?News {
		try {
			return News::hydrateNews($row);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
	}

	public static function hydrateNewsList(array $rows): array {
		$news = [];
		foreach ($rows as $row) {
			$news[] = News::hydrateNews($row);
		}
		return $news;
	}
	public static function tryHydrateNewsList(array $rows): array {
		$news = [];
		foreach ($rows as $row) {
			$row = News::tryHydrateNews($row);
			if ($row !== null) {
				$news[] = $row;
			}
		}
		return $news;
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
		return $this->createdAt;
	}
	public function setCreatedAt(DateTimeInterface $date): void {
		$this->createdAt = $date;
	}

	public function __tostring(): string {
		return json_encode([
			'id' => $this->id,
			'content' => $this->content,
			'createdAt' => $this->createdAt->format(\DateTime::ATOM)
		]);
	}
}