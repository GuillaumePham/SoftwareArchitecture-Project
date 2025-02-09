<?php

declare(strict_types=1);

namespace App\VO;

class Event {
	/** @var array<callable> */
	private $listeners = [];

	/**
	 * @template T
	 * @param callable(T): void $listener
	 */
	public function subscribe(callable $listener): void {
		$this->listeners[] = $listener;
	}

	/**
	 * @template T
	 * @param callable(T): void $listener
	 */
	public function unsubscribe(callable $listener): void {
		$this->listeners = array_filter($this->listeners, function($l) use ($listener) {
			return $l !== $listener;
		});
	}

	/**
	 * @template T
	 * @param T ...$args
	 */
	public function dispatch(...$args): void {
		foreach ($this->listeners as $listener) {
			call_user_func_array($listener, $args);
		}
	}
}