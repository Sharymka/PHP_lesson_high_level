<?php

use Geekbrains\LevelTwo\Blog\Container\DIContainer;
use Geekbrains\LevelTwo\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;

    // Подключаем автозагрузчик Composer
    require_once __DIR__ . '/vendor/autoload.php';
    // Создаём объект контейнера ..
    $container = new DIContainer();
    // .. и настраиваем его:
    // 1. подключение к БД
    $container->bind(
        PDO::class,
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
    );
// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUserRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);
// Возвращаем объект контейнера
return $container;