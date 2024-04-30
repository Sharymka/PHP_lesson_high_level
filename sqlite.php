<?php

use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;

require_once __DIR__ .  "/vendor/autoload.php";

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$uuid = UUID::random();
$uuidString = $uuid->getUuidString();

$repository = new SqliteUserRepository($connection);

$user = new User($uuid, new Name('first_name', 'last_name'), 'username9', 'password');

$repository->save($user);



