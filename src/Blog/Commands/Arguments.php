<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

use Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;

class Arguments
{
    private array $arguments = [];

    /**
     * @throws ArgumentsException
     */
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
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

// Переносим сюда логику разбора аргументов командной строки

    /**
     * @throws ArgumentsException
     */
    public static function fromArgv(array $argv): self
    {
//        var_dump($argv);
        $arguments = [];
        foreach ($argv as $argument) {
            $parts = explode('=', $argument);
            if (count($parts) !== 2) {
                continue;
            }
            $arguments[$parts[0]] = $parts[1];
        }
        return new self($arguments);
    }

    /**
     * @throws ArgumentsException
     */
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