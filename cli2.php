<?php

//use PHP\src\Person\Name;
//require_once 'src/Person/Name.php';

require_once __DIR__ .  "/vendor/autoload.php";

spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        print_r('it is here');
        include $file;
    }
});

$name = new Geekbrains\LevelTwo\Person\Name ( 'Ivan', 'Ivanov');

$faker = Faker\Factory::create('ru_RU');

echo $faker->name() . PHP_EOL;

echo $name . PHP_EOL;