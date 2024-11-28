<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

use App\Model\News;
use App\VO\Uid;
use App\NewsEntityManager;


$manager = new NewsEntityManager();

// clear for testing
$manager->getDbAdapter()->clearAll();


$firstNews = $manager->getById(new Uid("1"));
echo $firstNews . PHP_EOL;

$createdNews = $manager->create(
	new News(
		new Uid("1"),
		"First news" ,
		new DateTimeImmutable("2021-10-10 10:10:10")
	)
);
echo $createdNews . PHP_EOL;

$firstNews = $manager->getById(new Uid("1"));
echo $firstNews . PHP_EOL;

$updateNews = $manager->update(
	new News(
		new Uid("1"),
		"Edited" ,
		new DateTimeImmutable("1969-12-31 23:59:59")
	)
);
echo $updateNews . PHP_EOL;

$manager->delete(new Uid("1"));

$firstNews = $manager->getById(new Uid("1"));
echo $firstNews . PHP_EOL;





