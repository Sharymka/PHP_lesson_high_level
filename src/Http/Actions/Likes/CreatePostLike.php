<?php

namespace Geekbrains\LevelTwo\Http\Actions\Likes;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\LikeAlreadyExistsException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostLikeNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Like;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\PostLikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePostLike implements ActionInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private PostLikesRepositoryInterface $postLikesRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws LikeAlreadyExistsException
     */
    public function handle(Request $request): Response
    {

        try {
           $user = $this->authentication->user($request);
        }catch(AuthException|UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try{
            $postUuid = $request->jsonBodyField('post_uuid');
        }catch(HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try{
             $post = $this->postsRepository->get(new UUID($postUuid));
        } catch(PostNotFoundException|UserNotFoundException $e) {
            $this->logger->warning("Post not found: uuid[$postUuid]");
            return new ErrorResponse($e->getMessage());
        }
//        try{
//            $user = $this->usersRepository->get(new UUID($userUuid));
//        } catch(UserNotFoundException $e) {
//            $this->logger->warning("User not found: uuid[$userUuid]");
//            return new ErrorResponse($e->getMessage());
//        }

        try{
            $this->postLikesRepository->checkUserLikeForPostExists($user->uuid(), $postUuid );
        } catch(LikeAlreadyExistsException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $likeUuid = UUID::random();

            $like = new Like (
                $likeUuid,
                $postUuid,
                $user->uuid());

                $this->postLikesRepository->save($like);


        $this->logger->info("PostLike created: uuid[$likeUuid]");

       return new SuccessfulResponse([
           'save' => 'done',
           'uuid' =>(string)$like->uuid()
       ]);
    }
}