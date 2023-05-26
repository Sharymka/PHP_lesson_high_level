<?php

namespace Geekbrains\LevelTwo\UnitTests\Commands;

use Geekbrains\LevelTwo\Blog\Commands\Arguments;
use Geekbrains\LevelTwo\Blog\Commands\CreatePostCommand;
use Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;
use Geekbrains\LevelTwo\UnitTests\DummyLogger;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CreatePostCommandTest extends TestCase
{

    /**
     * @throws Exception
     */
    function testItReturnPostByUuid()
    {
        $userRepositoryStub = $this->createStub(SqliteUserRepository::class);
        $postRepositoryStub = $this->createStub(SqlitePostRepository::class);
        $logger = new DummyLogger();

        $postRepositoryStub->method('get')->willReturn(
            new Post(
                new UUID('805e5cd4-b158-4556-bce4-53a7139b33d1'),
                new User(
                    new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'),
                    new Name('Linda', 'Petrova'),
                    'linda234',
                    '123'
                ),
                'title',
                'text'));

        $command = new CreatePostCommand($userRepositoryStub, $postRepositoryStub, $logger);
        $post = $command->getPost(new UUID('f9cdfe1c-1a03-4786-89a4-f4a871696928'));

        $this->assertSame('805e5cd4-b158-4556-bce4-53a7139b33d1', (string)$post->uuid());
        $this->assertSame('Linda', $post->user()->name()->getFirstName());
        $this->assertSame('Petrova', $post->user()->name()->getLastName());
        $this->assertSame('linda234',  $post->user()->username());
        $this->assertSame('123', $post->user()->getPassword());
    }

    /**
     * @throws CommandException
     * @throws Exception
     */


    /**
     * @throws Exception
     * @throws CommandException
     * @throws ArgumentsException
     */
    function testItSavePostToRepository()
    {
        $postsRepository = new class implements PostsRepositoryInterface {
            private bool $called = false;
            public function save(Post $post): void
            {
                $this->called = true;
            }
            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException("Not found");
            }
            public function getByUsername(string $username): Post
            {
                throw new PostNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid)
            {
                // TODO: Implement delete() method.
            }
        };

        $logger = new DummyLogger();
        $userRepositoryStub = $this->createStub(SqliteUserRepository::class);

        $command = new CreatePostCommand($userRepositoryStub, $postsRepository, $logger);
        $command->handle(new Arguments([
            'author_uuid'=> 'f9cdfe1c-1a03-4786-89a4-f4a871696123',
            'title' => 'title',
            'text' => 'text'
        ]));

        $this->assertTrue($postsRepository->wasCalled());

    }

}