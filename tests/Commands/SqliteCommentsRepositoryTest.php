<?php

namespace Geekbrains\LevelTwo\Commands;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    function testItSavesCommentToDatabase() {

        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);



        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid'=>'de6ee81b-bbef-4411-a29a-48138689ed85',
                ':post_uuid'=> 'e1b3db34-f69f-4425-bbfb-d437ed08a0a1',
                ':author_uuid'=> 'e15f6930-4a94-4f01-9d6c-3455133b3c54',
                ':text'=> 'text text text'
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $commentRepository = new SqliteCommentRepository($connectionStub);

        $user = new User(
            new UUID('e15f6930-4a94-4f01-9d6c-3455133b3c54'),
            new Name('Svetlana','Ivanova'),
            'sveta123'
        );

        $post = new Post(
            new UUID('e1b3db34-f69f-4425-bbfb-d437ed08a0a1'),
            $user,
            'title',
            'text'
        );

        $commentRepository->save(new Comment(
            new UUID('de6ee81b-bbef-4411-a29a-48138689ed85'),
            $post,
            $user,
            'text text text'
        ));
    }

    function testItGetsCommentByUuid() {

    }

    function testItTrowsAnExceptionWhenCommentNotFound() {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid'=>'f9cdfe1c-1a03-4786-89a4-f4a871696928',
            ]);

        $statementMock->method('fetch')->willReturn(false);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("Not found Comment with id: f9cdfe1c-1a03-4786-89a4-f4a871696928");

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentRepository($connectionStub);

        $repository->get(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));
    }
}