<?php
require_once __DIR__ .  "/vendor/autoload.php";


use Geekbrains\LevelTwo\Blog\Commands\CreateCommentCommand;
use Geekbrains\LevelTwo\Blog\Commands\CreatePostCommand;
use Geekbrains\LevelTwo\Blog\Commands\OtherArguments;
use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use \Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\UUID;


$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUserRepository($connection);
$commentRepository = new SqliteCommentRepository($connection);
$postRepository = new SqlitePostRepository($connection);

try {
//    $command = new CreateUserCommand($usersRepository);
//    $command->handle(Arguments::fromArgv($argv));

    //добавление комментария из командной строки
//    $commentCommand = new CreateCommentCommand($commentRepository);
//    $commentCommand->addComment((new Comment((new UUID(UUID::random()))->getUuidString(), '6666', '44444', OtherArguments::get($argv))));
//    $comment = $commentCommand->get((new UUID('1fce963f-e6ab-4861-9632-c95c37f8c755')));
//    var_dump($comment);

    // добавление поста из командной строки
    $postCommand = new CreatePostCommand($postRepository);
    $postCommand->addPost(new Post((new UUID(UUID::random()))->getUuidString(), '2345', 'wather',  OtherArguments::get($argv) ));
    $post = $postCommand->get(new UUID('805e5cd4-b158-4556-bce4-53a7139b33d1'));
    var_dump($post);

} catch (CommandException $ex) {
    echo $ex->getMessage();
}
