<?php

namespace Geekbrains\LevelTwo\Http\Actions\Likes;

use Geekbrains\LevelTwo\Blog\CommentLike;
use Geekbrains\LevelTwo\Blog\Exceptions\CommentLikeNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository\CommentLikesRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;

class CreateCommentLike implements ActionInterface
{

    public function __construct(
            private UsersRepositoryInterface $usersRepository,
            private CommentsRepositoryInterface $commentsRepository,
            private CommentLikesRepositoryInterface $commentLikesRepository

    )
    {
    }

    public function handle(Request $request): Response
    {
        try{
            $comment_uuid = $request->jsonBodyField('comment_uuid');
            $author_uuid = $request->jsonBodyField('author_uuid');
        }catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try{
            $comment = $this->commentsRepository->get( new UUID($comment_uuid));
        }catch (CommentNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }

        try{
            $user = $this->usersRepository->get( new UUID($author_uuid));
        }catch (UserNotFoundException $e){
            return new ErrorResponse($e->getMessage());
        }

        $newUuid = UUID::random();

        $this->commentLikesRepository->save(
            new CommentLike(
                $newUuid,
                $comment_uuid,
                $author_uuid
            )
        );
        return new SuccessfulResponse([
            'create' => 'done',
            'uuid' =>(string)$newUuid
        ]);
    }
}