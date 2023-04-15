<?php

namespace Geekbrains\LevelTwo\Person;

class Person
{


    /**
     * @param Name $name
     * @param \DateTimeImmutable $date
     */
    public function __construct(Name $name, \DateTimeImmutable $date)
    {
        $this->name = $name;
        $this->date = $date;
    }

    public function __toString() : string{
        return $this->name . ' на сайте с (' . $this->date->format('Y-m-d') . ') ';
    }
}