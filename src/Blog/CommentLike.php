<?php

namespace Geekbrains\LevelTwo\Blog;

class CommentLike
{
    public function __construct(
        private UUID $uuid,
        private string $comment_uuid,
        private string $user_uuid,

    )
    {
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function commentUuid(): string
    {
        return $this->comment_uuid;
    }

    /**
     * @return string
     */
    public function userUuid(): string
    {
        return $this->user_uuid;
    }
}