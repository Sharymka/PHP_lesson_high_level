<?php

namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\Post;

class Comment
{
    private int $id;
    private User $user;
    private Post $post;
    private string $text;

    /**
     * @param int $id
     * @param User $user
     * @param Post $post
     * @param string $text
     */
    public function __construct(int $id, User $user, Post $post, string $text)
    {
        $this->id = $id;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return 'пост: ' . $this->post->getText() . PHP_EOL . 'пользователь: ' .  $this->user->getUsername() .
            PHP_EOL . 'комментарий: ' . $this->text . PHP_EOL;
    }


}