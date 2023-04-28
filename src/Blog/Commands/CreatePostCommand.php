<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Person\Name;

class CreatePostCommand
{

    public function __construct(
        private SqliteUserRepository $userRepository,
        private SqlitePostRepository $postRepository

    )
    {
    }
     public function addPost(Post $post) {
        $this->postRepository->save($post);
     }

    /**
     * @throws CommandException
     */
    public function getPost(UUID $uuid) {
        $result =  $this->postRepository->get($uuid);
        return new Post(new UUID($result['uuid']), $this->getUser(new UUID($result['author_uuid'])), $result['title'], $result['text']);
     }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getUser(UUID $uuid) {
        return $this->userRepository->get($uuid);
    }
}