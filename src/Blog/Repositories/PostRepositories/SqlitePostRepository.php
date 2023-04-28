<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostRepositories;

use Geekbrains\LevelTwo\Blog\Comment;
use Geekbrains\LevelTwo\Blog\Exceptions\CommandException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqlitePostRepository
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
            ':author_uuid' => $post->uuid(),
            ':title' => $post->authorUuid(),
            ':text' => $post->text(),

        ]);

    }

    /**
     * @throws CommandException
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
            throw new CommandException('There is no such post!');
        }

        return new Post(
            $result['uuid'],
            $result['author_uuid'],
            $result['title'],
            $result['text']
        );
    }

}