<?php

namespace Geekbrains\LevelTwo\Blog;

/**
 *
 */
class Like
{

    public function __construct(
        private UUID $uuid,
        private string $post_uuid,
        private string $user_uuid,
    )
    {
    }

    /**
     * @return string
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function postUuid(): string
    {
        return $this->post_uuid;
    }

    /**
     * @return string
     */
    public function userUuid(): string
    {
        return $this->user_uuid;
    }
}