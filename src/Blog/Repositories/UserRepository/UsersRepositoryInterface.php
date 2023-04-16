<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\UserRepository;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;

    public function getByUsername(string $username): User;
}

