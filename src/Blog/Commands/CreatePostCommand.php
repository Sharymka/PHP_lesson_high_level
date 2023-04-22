<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\UUID;

class CreatePostCommand
{


    public function __construct(
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
    public function get(UUID $uuid): Post {
        return  $this->postRepository->get($uuid);
     }
}