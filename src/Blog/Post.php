<?php

namespace Geekbrains\LevelTwo\Blog;

class Post
{
    private UUID $uuid;
    private User $user;
    private string $title;
    private string $text;



    /**
     * @param UUID $uuid
     * @param User $user
     * @param string $text
     */
    public function __construct(UUID $uuid, User $user, string $title, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;

    }

    public function __toString(): string
    {
        return   $this->title . ' : ' .' ' . $this->user->name() . 'пишет: ' . $this->text .PHP_EOL;
    }

    /**
     * @return User
     */
    public function user(): User
    {
        return $this->user;
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
    public function text(): string
    {
        return $this->text;
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

}