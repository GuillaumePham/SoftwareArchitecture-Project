<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

use App\Model\News;
use App\VO\Uid;
use App\NewsEntityManager;


$manager = new NewsEntityManager();

$manager->clear();

$firstNews = $manager->getById(new Uid("1"));
var_dump($firstNews);

$createdNews = $manager->create(
	new News(
		new Uid("sex"),
		"uWU" ,
		new DateTimeImmutable("2021-10-10 10:10:10")
	)
);
var_dump($createdNews);

$updateNews = $manager->update(
	new News(
		new Uid("1"),
		" HAHA Modified" ,
		new DateTimeImmutable("2021-10-10 10:10:10")
	)
);
var_dump($updateNews);

$manager->delete(new Uid("1"));





