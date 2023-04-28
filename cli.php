<?php
require_once __DIR__ .  "/vendor/autoload.php";


use Geekbrains\LevelTwo\Blog\Commands\Arguments;
use Geekbrains\LevelTwo\Blog\Commands\CreateCommentCommand;
use Geekbrains\LevelTwo\Blog\Commands\CreatePostCommand;
use Geekbrains\LevelTwo\Blog\Commands\CreateUserCommand;
use Geekbrains\LevelTwo\Blog\Commands\OtherArguments;
use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository\SqliteCommentRepository;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use \Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\UUID;
use \Geekbrains\LevelTwo\Blog\Exceptions\ArgumentsException;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Person\Name;

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUserRepository($connection);
$commentRepository = new SqliteCommentRepository($connection);
$postRepository = new SqlitePostRepository($connection);

try {

    $postRepository->delete(new UUID('02000775-c963-43a3-b644-36bf95caf7c4'));
//    $command = new CreateUserCommand($usersRepository);
//    $command->handle(Arguments::fromArgv($argv));

    //добавление комментария из командной строки
//    $commentCommand = new CreateCommentCommand($commentRepository);
//    $commentCommand->addComment((new Comment((new UUID(UUID::random()))->getUuidString(), '6666', '44444', OtherArguments::get($argv))));
//    $comment = $commentCommand->get((new UUID('1fce963f-e6ab-4861-9632-c95c37f8c755')));
//    var_dump($comment);

//    $user = new User(new UUID(UUID::random()), new Name('Lev', 'Petrushin'), 'lev2022');
//    // добавление поста из командной строки
//    $postCommand = new CreatePostCommand($usersRepository, $postRepository);
////    $postCommand->addPost(new Post((new UUID(UUID::random())), $user, 'wather',  OtherArguments::get($argv) ));
//    $post = $postCommand->getPost(new UUID('acbc10fe-78a2-4c47-8833-8ebbc34a9bcb'));
//    var_dump($post);

} catch (CommandException $ex) {
    echo $ex->getMessage();
} catch (ArgumentsException $e) {
    echo $e->getMessage();
}
