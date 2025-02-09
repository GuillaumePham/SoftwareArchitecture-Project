<?php

require_once dirname(__FILE__) . '/vendor/autoload.php';

use App\Model\User;
use App\VO\Uid;
use App\Controller;


$controller = new Controller();
$controller->clear();

$manager = $controller->getUserManager();

// Try to get a non-existent user
$firstUser = $manager->getById(new Uid("1"));
echo "Non-existent user (should be empty): " . $firstUser . PHP_EOL;

// Create a user
$createdUser = $manager->create(
    new User(
        new Uid("1"),
        "JohnDoe",
        "password123",
        "teliyig121@perceint.com",
        new DateTimeImmutable("2024-02-09 12:00:00")
    )
);
echo "Created user: " . $createdUser . PHP_EOL;

$createdUser = $manager->create(
    new User(
        new Uid("2"),
        "AxelSevenet",
        "password123",
        "axelsevenet@gmail.com",
        new DateTimeImmutable("2024-02-09 12:00:00")
    )
);
echo "Created user: " . $createdUser . PHP_EOL;


$controller->clear();

//// Retrieve the user
//$firstUser = $manager->getById(new Uid("1"));
//echo "Retrieved user: " . var_export($firstUser, true) . PHP_EOL;
//
//// Update the user
//$updatedUser = $manager->update(
//    new User(
//        new Uid("1"),
//        "JohnDoeUpdated",
//        "newpassword456",
//        "johndoe_updated@example.com",
//        new DateTimeImmutable("2025-01-01 00:00:00")
//    )
//);
//echo "Updated user: " . var_export($updatedUser, true) . PHP_EOL;
//
//// Delete the user
//$manager->delete(new Uid("1"));
//
//// Try to get the deleted user
//$firstUser = $manager->getById(new Uid("1"));
//echo "User after deletion (should be null): " . var_export($firstUser, true) . PHP_EOL;
