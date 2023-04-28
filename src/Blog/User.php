<?php
namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     */
    public function __construct(UUID $uuid, Name $name, string $username)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $username;
    }

    public function __toString(): string
    {
        return "пользователь $this->uuid с именем $this->name и логином $this->username ";
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }
}