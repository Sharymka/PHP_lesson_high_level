<?php

namespace Geekbrains\LevelTwo\UnitTests\Commands;

use Geekbrains\LevelTwo\Blog\Commands\Arguments;
use Geekbrains\LevelTwo\Blog\Commands\CreateUserCommand;
use Geekbrains\LevelTwo\Blog\Commands\Users\CreateUser;
use Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\DummyUsersRepository;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use Geekbrains\LevelTwo\UnitTests\DummyLogger;
use PhpParser\Node\Expr\Array_;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{

    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository([]),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'username' => 'Ivan',
        ]));
    }

    public function testItRequiresPasswordNew(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository([])
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password"'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresFirstNameNew(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository([])
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }


    // Проверяем, что команда создания пользователя бросает исключение,
// если пользователь с таким именем уже существует
    /**
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
    // Создаём объект команды
    // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand($this->makeUsersRepository([
            new User(
                new UUID("86a34c9e-623d-4058-ae0e-a354aafe9e66"),
                new Name('Ivan', 'Petrov'),
                'Ivan',
                '123'
            )
        ]),
        new DummyLogger());
    // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User already exists: Ivan");
        // и его сообщение
    // Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan','password' => '123']));
    }

//    public function testItSavesUserToRepositoryNew(): void
//    {
//        $usersRepository = new class implements UsersRepositoryInterface {
//
//            private bool $called = false;
//            public function save(User $user): void
//            {
//                $this->called = true;
//            }
//
//            public function get(UUID $uuid): User
//            {
//                throw new UserNotFoundException("Not found");
//            }
//
//            public function getByUsername(string $username): User
//            {
//                foreach ($this->users as $user) {
//                    if($user->username() == $username) {
//                        return $user;
//                    }
//                }
//                throw new UserNotFoundException("Not found");
//            }
//
//            public function wasCalled() {
//                return $this->called;
//            }
//        };
//
//        $command = new CreateUser(
//            $usersRepository
//        );
//        $command->run(
//            new ArrayInput([
//                'username' => 'Ivan',
//                'password' => 'some_password',
//                'first_name' => 'Ivan',
//                'last_name' => 'Nikitin',
//            ]),
//            new NullOutput()
//        );
//        $this->assertTrue($usersRepository->wasCalled());
//    }

    private function makeUsersRepository($users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if($user->username() == $username) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }


        public function testItRequiresLastName(): void
    {
// Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository([]), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123',
            'first_name' => 'Ivan'
        ]));
    }

    public function testItRequiresLastNameNew(): void
    {
// Тестируем новую команду
        $command = new CreateUser(
            $this->makeUsersRepository([]),
        );
// Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
// .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );
// Запускаем команду методом run вместо handle
        $command->run(
// Передаём аргументы как ArrayInput,
// а не Arguments
// Сами аргументы не меняются
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
// Передаём также объект,
// реализующий контракт OutputInterface
// Нам подойдёт реализация,
// которая ничего не делает
            new NullOutput()
        );
    }

// Тест проверяет, что команда действительно требует имя пользователя

    /**
     * @throws CommandException
     */
    public function testItRequiresFirstName(): void
    {
// Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository([]), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        $command->handle(new Arguments(['username' => 'Ivan',  'password' => '123',]));
    }

    /**
     * @throws ArgumentsException
     * @throws CommandException
     */
    public function testItSavesUserToRepository(): void
    {
    // Создаём объект анонимного класса
            $usersRepository = new class implements UsersRepositoryInterface {
    // В этом свойстве мы храним информацию о том,
    // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void
            {
    // Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
        // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
        // Запускаем команду
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password' => '123'
        ]));
        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }
}