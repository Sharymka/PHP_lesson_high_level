<?php

namespace Geekbrains\LevelTwo\Http\Auth;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Http\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
// 2. Аутентифицируем пользователя
// Проверяем, что предъявленный пароль
// соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

// Пользователь аутентифицирован
        return $user;
    }
}