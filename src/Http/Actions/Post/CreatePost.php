<?php

namespace Geekbrains\LevelTwo\Http\Actions\Post;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\Auth\AuthenticationInterface;
use Geekbrains\LevelTwo\Http\Auth\IdentificationInterface;
use Geekbrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger
    )
    {
    }

    public function  handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        }catch(AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $newPostUuid = UUID::random();

        try {
        // Пытаемся создать объект статьи
        // из данных запроса
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField("title"),
                $request->jsonBodyField("text"),
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");
        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        return new SuccessfulResponse([
            'create' => 'done',
            'uuid' => (string)$newPostUuid
        ]);

    }
}