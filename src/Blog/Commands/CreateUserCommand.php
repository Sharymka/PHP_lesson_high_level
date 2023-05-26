<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger

    ) {
        }
// Вместо массива принимаем объект типа Arguments

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');
        $password = $arguments->get('password');

        $hash = hash('sha256', $password);

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }

        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );


        $this->usersRepository->save($user);

        // Получаем UUID созданного пользователя
        $this->logger->info('User created: ' . $user->uuid());
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $exception) {
            return false;
        }
        return true;
    }
}
