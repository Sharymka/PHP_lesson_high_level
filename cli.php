<?php
require_once __DIR__ .  "/vendor/autoload.php";


use Geekbrains\LevelTwo\Blog\Commands\CreateUserCommand;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Person\Name;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use \Geekbrains\LevelTwo\Blog\UUID;

//$drivers = PDO::getAvailableDrivers();
//var_dump($drivers);
//die;

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

//Создаём объект репозитория
$usersRepository = new SqliteUserRepository($connection);
//Добавляем в репозиторий несколько пользователей

//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'),'admin'));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), 'guest'));
//
try {
    $command = new CreateUserCommand($usersRepository);
    $command->handle($argv);
//    $user2 = $usersRepository->get(new UUID('f5188b-cd3b-45ad-9e97-e9c7659ccd0e'));
//    $user3 = $usersRepository->getByUsername('admin');
//    echo $user3;
} catch (CommandException $ex) {
    echo $ex->getMessage();
}
