<?php

namespace Geekbrains\LevelTwo\Blog;

use Geekbrains\LevelTwo\Person\Person;

class Post
{
    private int $id;
    private User $person;
    private string $text;
    private string $title;

    /**
     * @param int $id
     * @param Person $person
     * @param string $text
     */
    public function __construct(int $id, User $person, string $text, string $title)
    {
        $this->id = $id;
        $this->person = $person;
        $this->text = $text;
        $this->title = $title;
    }

    public function __toString(): string
    {
        return   $this->title . ' : ' .' ' . $this->person . 'пишет: ' . $this->text .PHP_EOL;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getPerson(): User
    {
        return $this->person;
    }

    /**
     * @param User $person
     */
    public function setPerson(User $person): void
    {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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