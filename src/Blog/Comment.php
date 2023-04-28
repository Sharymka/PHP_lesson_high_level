<?php

namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\Post;

class Comment
{
    private string $uuid;
    private string $author_uuid;
    private string $post_uuid;
    private string $text;

    /**
     * @param string $uuid
     * @param string $author_uuid
     * @param string $post_uuid
     * @param string $text
     */
    public function __construct(string $uuid, string $author_uuid, string $post_uuid, string $text)
    {
        $this->uuid = $uuid;
        $this->author_uuid = $author_uuid;
        $this->post_uuid = $post_uuid;
        $this->text = $text;
    }

//    public function __toString(): string
//    {
//        return 'пост: ' . $this->post->getText() . PHP_EOL . 'пользователь: ' .  $this->user->getUsername() .
//            PHP_EOL . 'комментарий: ' . $this->text . PHP_EOL;
//    }

    /**
     * @return int
     */
    public function authorUuid(): string
    {
        return $this->author_uuid;
    }

    /**
     * @return int
     */
    public function postUuid(): string
    {
        return $this->post_uuid;
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