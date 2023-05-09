<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\PostLikesRepository;

use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\LikeAlreadyExistsException;
use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Like;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqlitePostLikesRepository implements LikesRepositoryInterface
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
    public function getByPostUuid(string $postUuid): ?array
    {

        $statement = $this->connection->prepare(
            "SELECT * FROM likes WHERE (post_uuid = :postUuid)"
        );
        $statement->execute([
            ":postUuid" => $postUuid
        ]);
        $allLikes = [];

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $allLikes[] =
                new Like(
                    new UUID($result['uuid']),
                    $result['post_uuid'],
                    $result['author_uuid']
                );
        }
        if(count($allLikes) == 0) {
            return null;
        }
        return $allLikes;
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function save(Like $newlike): void
    {
        $likes = $this->getByPostUuid($newlike->postUuid());
        if($likes !== null) {
            foreach ($likes as $like) {
                if((string)$like->userUuid() == $newlike->userUuid()) {
                    throw new LikeAlreadyExistsException("Post can not be liked more than once by the same user");
                }
            }
        }

        $statement = $this->connection->prepare(
            "INSERT INTO likes (uuid, post_uuid, author_uuid)
              VALUES (:uuid, :post_uuid, :author_uuid)"
        );

        $statement->execute([
            ":uuid" => $newlike->uuid(),
            ":post_uuid" => $newlike->postUuid(),
            ":author_uuid" => $newlike->userUuid()
        ]);

    }
}