<?php

require_once __DIR__ .  "/vendor/autoload.php";

use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\User;
use PHP\highLevel\Person\Name;
use PHP\highLevel\Person\Person;
use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Repositories\InMemoryUserRepository;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;

//spl_autoload_register('load');

//function load($className) {
//    var_dump($className);
//    $file = $className . '.php';
//    $file2 = str_replace("\\", "/", $file);
//    $file3 = str_replace('Geekbrains/LevelTwo', 'src', $file2);
//    var_dump($file3);
//    if(file_exists($file3)) {
//        require $file3;
//    }
//}
//
//$name = new Name('Ivan', 'Ivanov');
//$user = new User(1, $name, 'admin');
//$person = new Person($name, new DateTimeImmutable());
//
//echo $name . PHP_EOL;
//echo $user . PHP_EOL;
//echo $person . PHP_EOL;

$faker = Faker\Factory::create('ru_Ru');

$name = new Name( $faker->firstName(), $faker->lastName());
$user = new User($faker->randomDigit(), $name, 'admin');
$post = new Post($faker->randomDigit(),$user, $faker->title(2), $faker->sentence(5));

$user2 = new User(
    $faker->randomDigit(),
    new Name( $faker->firstName(), $faker->lastName()),
    'admin');
$user3 = new User(
    $faker->randomDigit(),
    new Name( $faker->firstName(), $faker->lastName()),
    'rrrrr');


$repository = new InMemoryUserRepository();
$repository->save($user);
$repository->save($user2);
$repository->save($user3);
try {
    $user = $repository->get($user->getId());
    print $user->getUsername();
} catch (UserNotFoundException $exception) {
    print $exception->getMessage();
}


foreach ($argv as $item) {
    switch ($item) {
        case 'user':
            echo $user;
            break;
        case 'post':
            echo new Post($faker->randomDigit(), $user,$faker->sentence(5), $faker->sentence(2));
            break;
        case 'comment':
            echo new Comment($faker->randomDigit(), $user, $post, $faker->sentence(5));
            break;
    }
}
