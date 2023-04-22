<?php

namespace Geekbrains\LevelTwo\Blog\Commands;

class OtherArguments
{


    public function __construct(
        private string $text
    ) {
    }
    public static function get(Array $argv): string{
        //    var_dump($argv);
        array_shift($argv);
        //    var_dump($argv);
        return implode(' ',$argv);
    }

}