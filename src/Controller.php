<?php

declare(strict_types=1);

namespace App;

use App\Model\News;
use App\Model\User;
use App\VO\Uid;
use App\Adapter\MySqlDbAdapter;
use App\Service\EmailService;
use App\Manager\NewsEntityManager;
use App\Manager\UserEntityManager;

class Controller {
	private $config;
	private $adapter;
	private $emailService;
	private $newsManager;
	private $userManager;

	public function __construct() {
		$this->config = parse_ini_file('config.ini');

		$this->adapter = new MySqlDbAdapter(
			host: $this->config['host'],
			db: $this->config['db'],
			user: $this->config['user'],
			password: $this->config['password']
		);

		$this->emailService = new EmailService();

		$this->newsManager = new NewsEntityManager($this->adapter);
		$this->userManager = new UserEntityManager($this->adapter);



		$this->newsManager->getCreateNewsEvent()->subscribe(function(News $news) {
			$users = $this->userManager->getAll();
			foreach ($users as $user) {
				$this->emailService->sendEmailTo($user, "News Created", "A new news has been created.");
			}
		});

		$this->newsManager->getCreateUserNewsEvent()->subscribe(function(News $news, User $user) {
			$otherUsers = array_filter($this->userManager->getAll(), function($otherUser) use ($user) {
				return $otherUser->getId() != $user->getId();
			});
			foreach ($otherUsers as $otherUser) {
				$this->emailService->sendEmailTo($otherUser, "News Created", "A new news has been created; A new user has joined us.");
			}
		});

		$this->userManager->getCreateUserEvent()->subscribe(function(User $user) {
			$this->emailService->sendEmailTo($user, "Account Created", "Your account has been successfully created.");
			$userId = $user->getId();
			$this->newsManager->createUserNews(new Uid("user_$userId"), $user);
		});

		$this->userManager->getUpdateUserEvent()->subscribe(function(User $user) {
			$this->emailService->sendEmailTo($user, "Account Updated", "Your account has been successfully updated.");
		});
	}

	public function getNewsManager(): NewsEntityManager { return $this->newsManager; }
	public function getUserManager(): UserEntityManager { return $this->userManager; }

	public function clear() {
		$this->adapter->clearTable($this->newsManager->getTableName());
		$this->adapter->clearTable($this->userManager->getTableName());
	}
}