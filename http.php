<?php
use Geekbrains\LevelTwo\Blog\Exceptions\AppException;
use Geekbrains\LevelTwo\Blog\Exceptions\NotFoundException;
use Geekbrains\LevelTwo\Http\Actions\Comment\CreateComment;
use Geekbrains\LevelTwo\Http\Actions\Likes\CreateCommentLike;
use Geekbrains\LevelTwo\Http\Actions\Post\CreatePost;
use Geekbrains\LevelTwo\Http\Actions\Post\DeletePost;
use Geekbrains\LevelTwo\Http\Actions\Users\CreateUser;
use Geekbrains\LevelTwo\Http\Actions\Users\FindByUsername;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Actions\Post\DeletePostByQuery;
use Geekbrains\LevelTwo\Http\Actions\Likes\CreatePostLike;


$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    // Добавили ещё один уровень вложенности
    // для отделения маршрутов,
    // применяемых к запросам с разными методами
    'GET' => [
        '/users/show' =>  FindByUsername::class,
    //        '/posts/show' =>  FindByUuid::class,
    ],
    'POST' => [
     // Добавили новый маршрут
        '/users/create' =>  CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/comments/create' => CreateComment::class,
        '/posts/delete' => DeletePost::class,
        '/likes/create' => CreatePostLike::class,
        '/commentLikes/create' => CreateCommentLike::class
    ],
    'DELETE' => [
        '/posts' => DeletePostByQuery::class
    ]
];


if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Method not found: $method $path"))->send();
    return;
}
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Path not found: $method $path"))->send();
    return;
}

// Получаем имя класса действия для маршрута
$actionClassName = $routes[$method][$path];
// С помощью контейнера
// создаём объект нужного действия
try {
    $action = $container->get($actionClassName);
} catch (NotFoundException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}


try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
    $response->send();



