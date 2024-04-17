<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostRepositories;

//use Geekbrains\LevelTwo\Blog\Comment;
//use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostsRepositoryException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
//use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\ErrorResponse;
//use Geekbrains\LevelTwo\Person\Name;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

//use PhpParser\Node\Expr\Array_;

class SqlitePostRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ) {

    }


    public function save(Post $post): void{

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
                VALUES (:uuid, :author_uuid, :title, :text)'
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->user()->uuid(),
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
            ':uuid'=> (string) $uuid,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$result) {
            throw new PostNotFoundException("Post not found: uuid [$uuid]");
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
        public function delete(UUID $uuid): void
        {
            try {
                $statement = $this->connection->prepare(
                    'DELETE FROM posts WHERE uuid = ?'
                );
                $statement->execute([(string)$uuid]);
            } catch (PDOException $e) {
                throw new PostsRepositoryException(
                    $e->getMessage(), (int)$e->getCode(), $e
                );
            }
        }

//        public function deleteAllData() {
//                $statement = $this->connection->prepare(
//                    ' TFROM TABLE posts '
//                );
//        }
}