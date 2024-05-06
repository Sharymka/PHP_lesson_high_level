<?php

namespace Geekbrains\LevelTwo\Http\Auth;

use DateTimeImmutable;
use Geekbrains\LevelTwo\Blog\AuthToken;
use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;

class LogIn  implements ActionInterface
{
    public function __construct(
    // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
    // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }
    public function handle(Request $request): Response
    {
    // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
    // Генерируем токен
        $authToken = new AuthToken(
    // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
     // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+6 day')
        );
    // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
    // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}