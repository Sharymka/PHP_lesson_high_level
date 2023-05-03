<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostRepositories;

//use Geekbrains\LevelTwo\Blog\Comment;
//use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
//use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\ErrorResponse;
//use Geekbrains\LevelTwo\Person\Name;
use PDO;
//use PhpParser\Node\Expr\Array_;

class SqlitePostRepository implements PostsRepositoryInterface
{
    private PDO $connection;
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }


    public function save(Post $post): void{

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
                VALUES (:uuid, :author_uuid, :title, :text)'
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text(),

        ]);

    }

    /**
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Post {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            'uuid'=> (string) $uuid,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$result) {
            throw new PostNotFoundException("Post not found Post: uuid [$uuid]");
        }

            $userRepository = new SqliteUserRepository($this->connection);
            $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }

    /**
     * @throws CommandException
     */
    public function delete(UUID $uuid){

        try{
            $this->get(new UUID($uuid));
        } catch(CommandException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $statement = $this->connection->prepare('DELETE FROM posts WHERE uuid = :uuid');
        $statement->execute([
            'uuid' =>(string)$uuid
        ]);
    }

}