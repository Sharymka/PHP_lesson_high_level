<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\UUID;

class CreateCommentCommand
{
    private SqliteCommentRepository $commentRepository;


    /**
 * @param SqliteCommentRepository $commentRepository
 */public function __construct(SqliteCommentRepository $commentRepository)
{
    $this->commentRepository = $commentRepository;
}

 /**
  * @return SqliteCommentRepository
  */
public function getCommentRepository(): SqliteCommentRepository
{
    return $this->commentRepository;
}



    public function addComment(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    public function get(UUID $uuid): Comment {
         return $this->commentRepository->get($uuid);
    }
}