<?php

namespace Geekbrains\LevelTwo\Http;

class ErrorResponse extends Response
{
    public function __construct(private string $message = 'Something goes wrong')
    {
    }

    protected const SUCCESS = false;

    protected function payload(): array
{
    return ['reason' => $this->message];
}
}
