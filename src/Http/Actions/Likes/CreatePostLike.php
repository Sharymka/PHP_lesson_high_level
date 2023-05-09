<?php

namespace Geekbrains\LevelTwo\Http\Actions\Likes;

use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\LikeAlreadyExistsException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Like;
use Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository\LikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;

class CreatePostLike implements ActionInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        private LikesRepositoryInterface $likesRepository
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
            return new ErrorResponse($e->getMessage());
        }
        try{
            $user = $this->usersRepository->get(new UUID($userUuid));
        } catch(UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $likeUuid = UUID::random();

            $like = new Like (
                $likeUuid,
                $postUuid,
                $userUuid);

            try{
                $this->likesRepository->save($like);
            }catch (LikeAlreadyExistsException $e) {
                return new ErrorResponse($e->getMessage());
            }

       return new SuccessfulResponse([
           'save' => 'done',
           'uuid' =>(string)$like->uuid()
       ]);
    }
}