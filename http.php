<?php
use Geekbrains\LevelTwo\Blog\Exceptions\AppException;
use Geekbrains\LevelTwo\Blog\Exceptions\NotFoundException;
use Geekbrains\LevelTwo\Http\Actions\Comment\CreateComment;
use Geekbrains\LevelTwo\Http\Actions\Likes\CreateCommentLike;
use Geekbrains\LevelTwo\Http\Actions\Post\CreatePost;
use Geekbrains\LevelTwo\Http\Actions\Post\DeletePost;
use Geekbrains\LevelTwo\Http\Actions\Users\CreateUser;
use Geekbrains\LevelTwo\Http\Actions\Users\FindByUsername;
use Geekbrains\LevelTwo\Http\Auth\LogIn;
use Geekbrains\LevelTwo\Http\Auth\LogOut;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Actions\Post\DeletePostByQuery;
use Geekbrains\LevelTwo\Http\Actions\Likes\CreatePostLike;
use Geekbrains\LevelTwo\Http\Response;
use Psr\Log\LoggerInterface;


$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' =>  FindByUsername::class,
//            '/posts/show' =>  FindByUuid::class,
    ],
    'POST' => [
     // Добавили новый маршрут
        '/users/create' =>  CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/posts/delete' => DeletePost::class,
        '/postLikes/create' => CreatePostLike::class,
        '/commentLikes/create' => CreateCommentLike::class,
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
    ],
    'DELETE' => [
        '/posts' => DeletePostByQuery::class
    ]
];

if (!array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])) {
// Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (Exception $e) {
// Логируем сообщение с уровнем ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
// Больше не отправляем пользователю
// конкретное сообщение об ошибке,
// а только логируем его
    (new ErrorResponse)->send();
    return;
}

$response->send();


