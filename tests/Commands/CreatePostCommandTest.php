<?php

namespace Geekbrains\LevelTwo\Commands;

use Geekbrains\LevelTwo\Blog\Commands\CreatePostCommand;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CreatePostCommandTest extends TestCase
{

    /**
     * @throws Exception
     */
    function testItReturnUserByUuid()
    {
        $UserRepositoryStub = $this->createStub(SqliteUserRepository::class);
        $PostRepositoryStub = $this->createStub(SqlitePostRepository::class);

        $UserRepositoryStub->method('get')->willReturn(
            new User( // Свойства пользователя точно такие,
// как и в описании мока
                new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'),
                new Name('Linda', 'Petrova'),
                'linda234',)
        );
        $command = new CreatePostCommand($UserRepositoryStub, $PostRepositoryStub);
        $user = $command->getUser(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));

        $this->assertSame('f9cdfe1c-1a03-4786-89a4-f4a871696928', (string)$user->uuid());
        $this->assertSame('Linda', $user->name()->getFirstName());
        $this->assertSame('Petrova', $user->name()->getLastName());
        $this->assertSame('linda234', $user->username());
    }

    /**
     * @throws CommandException
     * @throws Exception
     */
    function testItReturnPostById()
    {
        $UserRepositoryStub = $this->createStub(SqliteUserRepository::class);
        $PostRepositoryStub = $this->createStub(SqlitePostRepository::class);

        $PostRepositoryStub->method('get')->willReturn([
            'uuid' => 'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            'author_uuid' => 'f9cdfe1c-1a03-4786-89a4-f4a871696123',
            'title' => 'title',
            'text' => 'text'
        ]);

        $UserRepositoryStub->method('get')->willReturn(
            new User( // Свойства пользователя точно такие,
// как и в описании мока
                new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696123'),
                new Name('Ivan', 'Nikitin'),
                'ivan123',)
        );

        $command = new CreatePostCommand($UserRepositoryStub, $PostRepositoryStub);
        $post = $command->getPost(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));

        $this->assertSame('f9cdfe1c-1a03-4786-89a4-f4a871696928', (string)$post->uuid());
        $this->assertSame('f9cdfe1c-1a03-4786-89a4-f4a871696123', (string)$post->user()->uuid());
        $this->assertSame('title', $post->title());
        $this->assertSame('text', $post->text());

    }

    /**
     * @throws Exception
     * @throws CommandException
     */
    function testItSavePostToRepository()
    {
        $statementMock = $this->createMock(\PDOStatement::class);
        $connectionStub = $this->createStub(\PDO::class);
        $UserRepositoryStub = $this->createStub(SqliteUserRepository::class);
        $PostRepositoryStub = new SqlitePostRepository($connectionStub);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => 'f9cdfe1c-1a03-4786-89a4-f4a871696928',
                ':author_uuid' => 'f9cdfe1c-1a03-4786-89a4-f4a871696123',
                ':title' => 'title',
                ':text' => 'text'
            ]);

        $command = new CreatePostCommand($UserRepositoryStub, $PostRepositoryStub);
        $command->addPost(new Post(
            new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'),
            new User(
                new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696123'),
                new Name('first_name', 'last_name'),
                'usename'
            ),
            'title',
            'text'
        ));
    }

}