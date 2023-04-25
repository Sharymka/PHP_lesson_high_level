<?php

namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\User;

class Comment
{
    private string $uuid;
    private User $user;
    private Post $post;
    private string $text;

    /**
     * @param string $uuid
     * @param User $user
     * @param string $post_uuid
     * @param Post $post
     */
    public function __construct(string $uuid, User $user, Post $post, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return 'пост: ' . $this->post->text() . PHP_EOL . 'пользователь: ' .  $this->user->username() .
            PHP_EOL . 'комментарий: ' . $this->text . PHP_EOL;
    }

    /**
     * @return int
     */
    public function authorUuid(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function postUuid(): Post
    {
        return $this->post;
    }

    /**
     * @return int
     */
    public function uuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }


}