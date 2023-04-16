<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\UserRepository;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;

class InMemoryUserRepository implements UsersRepositoryInterface
{
    private array $users = [];

    /**
     * @param User $user
     */
    public function save(User $user) : void {
        $this->users[] = $user;
    }

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User {
        foreach ($this->users as $user) {
            if((string)$user->uuid() == (string)$uuid) {
                return $user;
            }
        }
        throw new  UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User {
        foreach ($this->users as $user) {
            if((string)$user->username() == $username) {
                return $user;
            }
    }
        throw new  UserNotFoundException("User not found: $username");
}