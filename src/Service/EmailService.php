<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;

class EmailService {
	public function sendEmailTo(User $user, string $subject, string $message): void {
		$this->sendEmail($user->getEmail(), $subject, $message);
	}

	private function sendEmail(string $to, string $subject, string $message): void {
		$headers = [
			'From' => "noreply@example.com",
			'Reply-To' => "noreply@example.com",
			'Content-Type' => "text/plain; charset=UTF-8"
		];

		if (!mail($to, $subject, $message, $headers)) {
			error_log("Failed to send email to $to");
		}
	}
}