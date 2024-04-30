<?php
namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Person\Name;
class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $username,
        private string $hashedPassword,
    ) {
    }
// Переименовали функцию
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }
// Функция для вычисления хеша
    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }
// Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }
// Функция для создания нового пользователя
    public static function createFrom(
        string $username,
        string $password,
        Name $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid)
        );
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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $username
     */
}