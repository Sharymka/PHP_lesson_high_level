<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\LikesRepository;

use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\LikeAlreadyExists;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Like;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqliteLikesRepository implements LikesRepositoryInterface
{

    public function __construct(
        private PDO $connection,
    )
    {
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByPostUuid(string $postUuid): Array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM likes WHERE (post_uuid = :postUuid)"
        );
        $statement->execute([
            ":postUuid" => $postUuid
        ]);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!$result) {
            throw new PostNotFoundException("Post inside likes not found: uuid [$postUuid]");
        }
        $allLikes = [];
        foreach ($result as $like) {
            $allLikes[] = new Like(
                new UUID($like['uuid']),
                $like['post_uuid'],
                $like['author_uuid']
            );
        }
        return new $allLikes;

    }

    /**
     * @throws PostNotFoundException
     */
    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            "INSERT INTO likes (uuid, post_uuid, author_uuid)
              VALUES (:uuid, :post_uuid, :author_uuid)"
        );

        $statement->execute([
            ":uuid" => $like->uuid(),
            ":post_uuid" => $like->postUuid(),
            ":author_uuid" => $like->userUuid()
        ]);

    }
}