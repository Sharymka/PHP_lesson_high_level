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
    private User $user;
    private Post $post;


    public function __construct(
        private SqlitePostRepository $postRepository,
        private SqliteUserRepository $userRepository
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
        return new Post(new UUID($result['uuid']), $this->getUser($result['author_uuid']), $result['title'], $result['text']);
     }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getUser(UUID $uuid) {
        $result =  $this->userRepository->get($uuid);
        return new User(new UUID($result['uuid']), new Name($result['first_name'], $result['last_name']), $result['username']);
    }
}