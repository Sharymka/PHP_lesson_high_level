<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository;

use Geekbrains\LevelTwo\Blog\CommentLike;
use Geekbrains\LevelTwo\Blog\UUID;

interface CommentLikesRepositoryInterface
{
    public  function save(CommentLike $like): void;
    public function get(UUID $uuid): CommentLike;
}