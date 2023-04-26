<?php

namespace Geekbrains\LevelTwo;

use Geekbrains\LevelTwo\Blog\Commands\CreatePostCommand;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;

class SqlitePostsRepositoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    function testItSavesPostToDatabase() {

        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid'=>'e15f6930-4a94-4f01-9d6c-3455133b3c54',
                ':author_uuid'=>'e15f6930-4a94-4f01-9d6c-3466133b3c77',
                ':title'=>'title',
                ':text'=>'text'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostRepository($connectionStub);
        $user = new User(
            new UUID('e15f6930-4a94-4f01-9d6c-3466133b3c77'),
            new Name('Svetlana','Ivanova'),
            'sveta123'
        );

        $repository->save(
            new Post(
                new UUID('e15f6930-4a94-4f01-9d6c-3455133b3c54'),
                $user,
                'title',
                'text'
            )

        );
    }

    /**
     * @throws Exception
     * @throws CommandException
     */
    function testItGetsPostByUuid() {

        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                'uuid' => 'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            ]);

        $statementMock->method('fetch')->willReturn([
            'uuid'=>'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            'author_uuid'=>'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            'title'=> 'title',
            'text'=> 'text'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostRepository($connectionStub);

        $post = $repository->get(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));

        $this->assertSame('f9cdfe1c-1a03-4786-89a4-f4a871696928', $post['uuid']);
    }

    function testItTrowsAnExceptionWhenPostNotFound(){
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                'uuid'=>'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            ]);

        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Not found Post with id: f9cdfe1c-1a03-4786-89a4-f4a871696928");

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostRepository($connectionStub);

        $repository->get(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));

    }

}