<?php

namespace Geekbrains\LevelTwo\Blog\Repositories\UserRepository;

use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Person\Name;

use \PDO;

class SqliteUserRepository implements UsersRepositoryInterface
{
    private PDO $connection;
    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }


    public function save(User $user): void{

        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
                VALUES (:uuid, :username, :first_name, :last_name)'
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
            ':first_name' => $user->name()->getFirstName(),
            ':last_name' => $user->name()->getLastName(),

        ]);

    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            'uuid'=> (string) $uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User {
        $statement = $this->connection->prepare('SELECT * FROM users WHERE username = :username');
        $statement ->execute([
           ':username'=> $username
        ]);

        return $this->getUser($statement, $username);

    }

    public function getUser(\PDOStatement $statement, string $str): User {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $str"
            );
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name'] ),
            $str
        );
    }
}