<?php

namespace Geekbrains\LevelTwo\Blog;


class Comment
{
    private string $uuid;
    private User $user;
    private Post $post;
    private string $text;

    /**
     * @param string $uuid
     * @param User $user
     * @param Post $post
     */
    public function __construct(string $uuid, Post $post, User $user, string $text)
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
    public function user(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function post(): Post
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