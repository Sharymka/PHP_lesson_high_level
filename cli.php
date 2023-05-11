<?php
require_once __DIR__ .  "/vendor/autoload.php";


use Geekbrains\LevelTwo\Blog\Commands\Arguments;
use Geekbrains\LevelTwo\Blog\Commands\CreateUserCommand;
use Geekbrains\LevelTwo\Blog\Exceptions\AppException;

$container = require __DIR__ . '/bootstrap.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}

