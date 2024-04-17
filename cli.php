<?php
require_once __DIR__ .  "/vendor/autoload.php";


use Geekbrains\LevelTwo\Blog\Commands\Arguments;
use Geekbrains\LevelTwo\Blog\Commands\CreateUserCommand;
use Geekbrains\LevelTwo\Blog\Commands\FakeData\PopulateDB;
use Geekbrains\LevelTwo\Blog\Commands\Posts\DeletePost;
use Geekbrains\LevelTwo\Blog\Commands\Users\CreateUser;
use Geekbrains\LevelTwo\Blog\Commands\Users\UpdateUser;
use Geekbrains\LevelTwo\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,

];
foreach ($commandsClasses as $commandClass) {
// Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
// Добавляем команду к приложению
    $application->add($command);
}


$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//
//$command = $container->get(CreateUserCommand::class);
$logger = $container->get(LoggerInterface::class);


try {
    // Запускаем приложение
    $application->run();
//    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo "{$e->getMessage()}\n";
}

