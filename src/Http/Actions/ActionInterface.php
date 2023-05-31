<?php

namespace Geekbrains\LevelTwo\Http\Actions;

use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}