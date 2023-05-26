<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqliteCommentRepository implements CommentsRepositoryInterface
{
    private PDO $connection;
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }


    public function save(Comment $comment): void{

        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
                VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':post_uuid' => $comment->post()->uuid(),
            ':author_uuid' => $comment->user()->uuid(),
            ':text' => $comment->getText(),

        ]);

    }

    /**
     * @throws CommandException
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid'=> (string) $uuid,
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$result) {
            throw new CommentNotFoundException("Not found Comment with id: $uuid");
        }

        $userRepository = new SqliteUserRepository($this->connection);
        $postRepository = new SqlitePostRepository($this->connection);

        $user = $userRepository->get(new UUID($result['author_uuid']));
        $post = $postRepository->get(new UUID( $result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }


}