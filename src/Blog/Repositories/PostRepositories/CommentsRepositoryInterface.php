<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostRepositories;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}