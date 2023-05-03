<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\LikesRepository;

use Geekbrains\LevelTwo\Blog\Like;

interface LikesRepositoryInterface
{
    public function getByPostUuid(string $uuid): Array;
    public function save(Like $like): void;
}