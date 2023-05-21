<?php

namespace Geekbrains\LevelTwo\Http\Actions\Likes;

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
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePostLike implements ActionInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        private PostLikesRepositoryInterface $postLikesRepository,
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
        try{
            $postUuid = $request->jsonBodyField('post_uuid');
            $userUuid = $request->jsonBodyField('author_uuid');
        }catch(HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try{
             $post = $this->postsRepository->get(new UUID($postUuid));
        } catch(PostNotFoundException|UserNotFoundException $e) {
            $this->logger->warning("Post not found: uuid[$postUuid]");
            return new ErrorResponse($e->getMessage());
        }
        try{
            $user = $this->usersRepository->get(new UUID($userUuid));
        } catch(UserNotFoundException $e) {
            $this->logger->warning("User not found: uuid[$userUuid]");
            return new ErrorResponse($e->getMessage());
        }

        try{
            $this->postLikesRepository->checkUserLikeForPostExists($userUuid, $postUuid );
        } catch(PostLikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $likeUuid = UUID::random();

            $like = new Like (
                $likeUuid,
                $postUuid,
                $userUuid);

            try{
                $this->postLikesRepository->save($like);
            }catch (LikeAlreadyExistsException $e) {
                return new ErrorResponse($e->getMessage());
            }

        $this->logger->info("PostLike created: uuid[$likeUuid]");

       return new SuccessfulResponse([
           'save' => 'done',
           'uuid' =>(string)$like->uuid()
       ]);
    }
}