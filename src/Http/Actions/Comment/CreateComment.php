<?php

namespace Geekbrains\LevelTwo\Http\Actions\Comment;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private LoggerInterface $logger
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->jsonBodyField('post_uuid');
            $userUuid = $request->jsonBodyField('author_uuid');
        }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        try{
            $post = $this->postsRepository->get(new UUID($postUuid));
        }catch(PostNotFoundException $e) {
            $this->logger->warning("Post not found: uuid[$postUuid]");
            return new ErrorResponse($e->getMessage());
        }

        try{
            $user = $this->usersRepository->get(new UUID($userUuid));
        }catch(UserNotFoundException $e) {
            $this->logger->warning("User not found: uuid[$userUuid]");
            return new ErrorResponse($e->getMessage());
        }

        $newUuid = UUID::random();


                $this->commentsRepository->save(
                    new Comment(
                        $newUuid,
                        $post,
                        $user,
                        $request->jsonBodyField('text')
                    )
                );

                $this->logger->info("Comment created: uuid[$newUuid]");


            return new SuccessfulResponse([
                'create' => 'done',
                'uuid' =>(string)$newUuid
            ]);
    }
}