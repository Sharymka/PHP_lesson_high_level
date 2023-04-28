<?php

namespace Geekbrains\LevelTwo\Blog;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
class UUID
{
    public function __construct(
        private string $uuidString
    ) {
        if (!uuid_is_valid($this->uuidString)) {
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    /**
     * @return string
     */
    public function getUuidString(): string
    {
        return $this->uuidString;
    }
    public function __toString(): string{
        return $this->uuidString;
    }

    public static function random(): self{
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }



}