<?php

use Geekbrains\LevelTwo\Blog\Container\DIContainer;
use Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository\CommentLikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository\SqliteCommentLikesRepository;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\LikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\SqlitePostLikesRepository;
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
    SqlitePostLikesRepository::class
);

$container->bind(
    CommentLikesRepositoryInterface::class,
    SqliteCommentLikesRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
   SqliteCommentRepository::class
);

// Возвращаем объект контейнера
return $container;