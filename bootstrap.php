<?php

use Geekbrains\LevelTwo\Blog\Container\DIContainer;
use Geekbrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository\CommentLikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository\SqliteCommentLikesRepository;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\PostLikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\SqlitePostLikesRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Http\Auth\AuthenticationInterface;
use Geekbrains\LevelTwo\Http\Auth\BearerTokenAuthentication;
use Geekbrains\LevelTwo\Http\Auth\IdentificationInterface;
use Geekbrains\LevelTwo\Http\Auth\JsonBodyUuidIdentification;
use Geekbrains\LevelTwo\Http\Auth\PasswordAuthentication;
use Geekbrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use Geekbrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;

    // Подключаем автозагрузчик Composer
    require_once __DIR__ . '/vendor/autoload.php';

    // Загружаем переменные окружения из файла .env
    Dotenv::createImmutable(__DIR__)->safeLoad();

    // Создаём объект контейнера ..
    $container = new DIContainer();

    $logger = (new Logger('blog'));
    // Включаем логирование в файлы,
    // если переменная окружения LOG_TO_FILES
    // содержит значение 'yes'
    if ('yes' === $_SERVER['LOG_TO_FILES']) {
        $logger
            ->pushHandler(new StreamHandler(
                __DIR__ . '/logs/blog.log'
            ))
            ->pushHandler(new StreamHandler(
                __DIR__ . '/logs/blog.error.log',
                level: Logger::ERROR,
                bubble: false,
            ));
    }
    // Включаем логирование в консоль,
    // если переменная окружения LOG_TO_CONSOLE
    // содержит значение 'yes'
    if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
        $logger
            ->pushHandler(
                new StreamHandler("php://stdout")
            );
}
    // .. и настраиваем его:
    // 1. подключение к БД
    $container->bind(
        PDO::class,
        new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
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
    PostLikesRepositoryInterface::class,
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

$container->bind(LoggerInterface::class,
    $logger
);

$container->bind(
    AuthenticationInterface::class,
    JsonBodyUuidIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);



// Возвращаем объект контейнера
return $container;