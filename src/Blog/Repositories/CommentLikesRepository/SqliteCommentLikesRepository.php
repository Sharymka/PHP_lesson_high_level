<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\CommentLikesRepository;

use Geekbrains\LevelTwo\Blog\CommentLike;
use Geekbrains\LevelTwo\Blog\Exceptions\CommentLikeAlreadyExistsException;
use Geekbrains\LevelTwo\Blog\Exceptions\CommentLikeNotFoundException;
use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\UUID;
use PDO;

class SqliteCommentLikesRepository implements CommentLikesRepositoryInterface
{

    public function __construct(
        private PDO $connection
    )
    {
    }

    /**
     * @throws CommentLikeAlreadyExistsException
     */
    public function save(CommentLike $newLike): void
    {
        $commentLikes = $this->getByUserUuid($newLike->userUuid());

        foreach ($commentLikes as $like) {
            if($like->commentUuid() == $newLike->commentUuid()) {
                throw new CommentLikeAlreadyExistsException("Comment can not be liked more than once by the same user");
            }
        }


        $statement = $this->connection->prepare(
            'INSERT INTO commentLikes (uuid, author_uuid, comment_uuid)
                    VALUES (:uuid, :author_uuid, :comment_uuid)');

        $statement->execute([
            ':uuid' => $newLike->uuid(),
            ':author_uuid' => $newLike->userUuid(),
            ':comment_uuid' => $newLike->commentUuid()
        ]);
    }

    /**
     * @throws CommentLikeNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): CommentLike
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM commentLikes WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(!$result) {
            throw new CommentLikeNotFoundException("CommentLike not found: uuid [$uuid]");
        }

        return new CommentLike(
            new UUID($result['uuid']),
            $result['comment_uuid'],
            $result['author_uuid']
        );
    }

    public function getByUserUuid(string $userUuid){
        $statement = $this->connection->prepare('
        SELECT * FROM commentLikes WHERE author_uuid = :author_uuid');

        $statement->execute([
            ':author_uuid' => $userUuid
        ]);

        $commentLikes = [];

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)){
            $commentLikes[] = new CommentLike(
                new UUID($result['uuid']),
                $result['comment_uuid'],
                $result['author_uuid']
            );
        }

        return $commentLikes;
    }
}