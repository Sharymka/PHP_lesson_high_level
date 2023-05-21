<?php

namespace Geekbrains\LevelTwo\Http\Auth;

use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Http\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}