<?php

namespace Geekbrains\LevelTwo\UnitTests;

use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use PHP\highLevel\Person\Name;
use PDO;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;

class SqliteUsersRepositoryTest extends TestCase
{

    /**
     * @throws UserNotFoundException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    function testItGetsUserByUuid(){
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'e1b3db34-f69f-4425-bbfb-d437ed08a0a1',
            'first_name' => 'Linda',
            'last_name' => 'Petrova',
            'username' => 'linda234',
            'password' => '123'
        ]);


        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryUser = new SqliteUserRepository($connectionStub);

        $user = $repositoryUser->get(new UUID('e1b3db34-f69f-4425-bbfb-d437ed08a0a1'));

        $this->assertSame('e1b3db34-f69f-4425-bbfb-d437ed08a0a1', (string)$user->uuid());
        $this->assertSame('Linda', $user->name()->getFirstName());
        $this->assertSame('Petrova', $user->name()->getLastName());
        $this->assertSame('linda234', $user->username());
        $this->assertSame('123', $user->getPassword());
    }


    /**
     * @throws Exception
     */
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
                ':password' => '123'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteUserRepository($connectionStub);

        $repository->save(
            new User( // Свойства пользователя точно такие,
// как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123',
                '123')
        );
    }
}