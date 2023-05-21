<?php

namespace Geekbrains\LevelTwo\Http\Auth;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }
    public function user(Request $request): User
    {
        try {
        // Получаем UUID пользователя из JSON-тела запроса;
        // ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
        // Если невозможно получить UUID из запроса -
        // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
        // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
        // Если пользователь с таким UUID не найден -
        // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}