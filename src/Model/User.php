<?php

declare(strict_types=1);

namespace App\Model;

use App\VO\Uid;

use DateTimeInterface;
use DateTimeImmutable;

class User implements Model{
	private Uid $id;
	private string $login;
	private string $password;
	private string $email;
	private DateTimeInterface $createdAt;

	public function __construct(
		Uid $id,
		string $login,
		string $password,
		string $email,
		DateTimeInterface $createdAt
	) {
		$this->id = $id;
		$this->login = $login;
		$this->password = $password;
		$this->email = $email;
		$this->createdAt = $createdAt;
	}

	public static function hydrateUser(array $row): User {
		if (!isset($row['id'])) {
			throw new \InvalidArgumentException('Missing id in row');
		}
		if (!isset($row['login'])) {
			throw new \InvalidArgumentException('Missing login in row');
		}
		if (!isset($row['password'])) {
			throw new \InvalidArgumentException('Missing password in row');
		}
		if (!isset($row['email'])) {
			throw new \InvalidArgumentException('Missing email in row');
		}
		if (!isset($row['created_at'])) {
			throw new \InvalidArgumentException('Missing created_at in row');
		}

		return new User(
			new Uid($row['id']),
			$row['login'],
			$row['password'],
			$row['email'],
			new DateTimeImmutable($row['created_at'])
		);
	}
	public static function tryHydrateUser(array $row): ?User {
		try {
			return User::hydrateUser($row);
		} catch (\InvalidArgumentException $e) {
			return null;
		}
	}
	public static function hydrateUserList(array $rows): array {
		$user = [];
		foreach ($rows as $row) {
			$user[] = User::hydrateUser($row);
		}
		return $user;
	}
	public static function tryHydrateUserList(array $rows): array {
		$user = [];
		foreach ($rows as $row) {
			$row = User::tryHydrateUser($row);
			if ($row !== null) {
				$user[] = $row;
			}
		}
		return $user;
	}

	public function getId(): Uid {
		return $this->id;
	}
	public function setId(Uid $id): void {
		$this->id = $id;
	}
	public function getLogin(): string {
		return $this->login;
	}
	public function setLogin(string $login): void {
		$this->login = $login;
	}
	public function getPassword(): string {
		return $this->password;
	}
	public function setPassword(string $password): void {
		$this->password = $password;
	}
	public function getEmail(): string {
		return $this->email;
	}
	public function setEmail(string $email): void {
		$this->email = $email;
	}
	public function getCreatedAt(): DateTimeInterface {
		return $this->createdAt;
	}
	public function setCreatedAt(DateTimeInterface $date): void {
		$this->createdAt = $date;
	}

	public function __tostring(): string {
		return sprintf(
			'[%s] %s %s %s',
			$this->createdAt->format(\DateTime::ATOM),
			$this->id,
			$this->login,
			$this->email
		);
	}
}
