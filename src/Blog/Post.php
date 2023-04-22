<?php

namespace Geekbrains\LevelTwo\Blog;

use Geekbrains\LevelTwo\Person\Person;

class Post
{
    private string $uuid;
    private string $author_uuid;
    private string $title;
    private string $text;


    /**
     * @param string $uuid
     * @param string $author_uuid
     * @param string $text
     */
    public function __construct(string $uuid, string $author_uuid,string $title, string $text)
    {
        $this->uuid = $uuid;
        $this->author_uuid = $author_uuid;
        $this->title = $title;
        $this->text = $text;

    }

//    public function __toString(): string
//    {
//        return   $this->title . ' : ' .' ' . $this->person . 'пишет: ' . $this->text .PHP_EOL;
//    }

    /**
     * @return string
     */
    public function authorUuid(): string
    {
        return $this->author_uuid;
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
    public function text(): string
    {
        return $this->text;
    }


    /**
     * @return string
     */
    public function getTitle(): string
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