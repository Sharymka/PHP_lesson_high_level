<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostRepositories;

use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Array;

    public function delete(UUID $uuid);

}