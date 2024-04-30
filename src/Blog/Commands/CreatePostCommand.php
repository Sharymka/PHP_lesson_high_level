<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use PHP\highLevel\Person\Name;
use Psr\Log\LoggerInterface;

class CreatePostCommand
{

    public function __construct(
        private SqliteUserRepository $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger

    )
    {
    }

    public function handle(Arguments $arguments) {

        $this->logger->info("Create post command started");

        $uuid = UUID::random();

        $author_uuid = $arguments->get('author_uuid');
        $user = $this->usersRepository->get(new UUID($author_uuid));

        $this->postsRepository->save( new Post(
            $uuid,
            $user,
            $arguments->get('title'),
            $arguments->get('text')
        ));
        $this->logger->info("User created: $uuid");
    }

    public function getPost(UUID $uuid): Post {
        return $this->postsRepository->get($uuid);
    }
}