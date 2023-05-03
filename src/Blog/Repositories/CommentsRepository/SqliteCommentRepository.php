<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\CommentsRepository;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqliteCommentRepository
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
            throw new CommandException("Not found Comment with id: $uuid");
        }

        return new Comment(
            $result['uuid'],
            $result['post_uuid'],
            $result['author_uuid'],
            $result['text']
        );
    }


}