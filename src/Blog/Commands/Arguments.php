<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;

class Arguments
{
    private array $arguments = [];
    public function __construct(iterable $arguments)
    {
        foreach ($arguments as $argument => $value) {
// Приводим к строкам
            $stringValue = trim((string)$value);
// Пропускаем пустые значения
            if (empty($stringValue)) {
                continue;
}
// Также приводим к строкам ключ
            $this->arguments[(string)$argument] = $stringValue;
        }
        var_dump($this->arguments);
    }
// Переносим сюда логику разбора аргументов командной строки
    public static function fromArgv(array $argv): self
    {
        $arguments = [];
        foreach ($argv as $argument) {
            $parts = explode('=', $argument);
            if (count($parts) !== 2) {
                continue;
            }
            $arguments[$parts[0]] = $parts[1];
        }
        var_dump($arguments);
        return new self($arguments);
    }
    public function get(string $argument): string
    {
        if (!array_key_exists($argument, $this->arguments)) {
            throw new ArgumentsException(
                "No such argument: $argument"
            );
        }
        return $this->arguments[$argument];
    }

}