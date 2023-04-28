<?php

namespace Geekbrains\LevelTwo\Blog\Exceptions;

use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
// Ничего не делаем
    }
    public function get(UUID $uuid): User
    {
// И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
    }
    public function getByUsername(string $username): User
    {
// Нас интересует реализация только этого метода
// Для нашего теста не важно, что это будет за пользователь,
// поэтому возвращаем совершенно произвольного
        return new User(UUID::random(), new Name("first", "last"),"user123");
    }
}
