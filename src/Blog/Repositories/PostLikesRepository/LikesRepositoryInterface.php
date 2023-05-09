<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository;

use Geekbrains\LevelTwo\Blog\Like;

interface LikesRepositoryInterface
{
    public function getByPostUuid(string $uuid): ?array;
    public function save(Like $like): void;
}