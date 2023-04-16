<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }
    public function handle(array $rawInput): void
    {
        $input = $this->parseRawInput($rawInput);
        $username = $input['username'];
// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }
// Сохраняем пользователя в репозиторий
    $this->usersRepository->save(new User(
        UUID::random(),
        new Name($input['first_name'], $input['last_name']),
        $username
    ));
}
    private function parseRawInput(array $rawInput): array
    {
        $input = [];
        foreach ($rawInput as $argument) {
            $parts = explode('=', $argument);
            if (count($parts) !== 2) {
                continue;
            }
            $input[$parts[0]] = $parts[1];
        }
        foreach (['username', 'first_name', 'last_name'] as $argument) {
            if (!array_key_exists($argument, $input)) {
                throw new CommandException(
                    "No required argument provided: $argument"
                );
            }
            if (empty($input[$argument])) {
                throw new CommandException(
"Empty argument provided: $argument"
            );
            }
        }
        return $input;
    }
    private function userExists(string $username): bool
    {
        try {
// Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $exception) {
            return false;
        }
        return true;
    }
}