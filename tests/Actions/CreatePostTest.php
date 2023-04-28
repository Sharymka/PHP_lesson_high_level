<?php

namespace Geekbrains\LevelTwo\Actions;

use Geekbrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\SqliteUserRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\Post\CreatePost;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use PDO;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Geekbrains\LevelTwo\Person\Name;

class CreatePostTest extends TestCase
{
//new Request([], [], '{"author_uuid":"e1b3db34-f69f-4425-bbfb-d437ed08a0a1", "title":"title", "text":"text"}');
    /**
     * @throws Exception
     * @throws \JsonException
     */
    function testItReTurnsSuccessfulResponse() {

        $createPost = $this->createMock(CreatePost::class);
        $request = $this->createStub(Request::class);

        $createPost->method('handle')->willReturn(
            new SuccessfulResponse([
                'create' => 'done',
                'uuid'=>'f9cdfe1c-1a03-4786-89a4-f4a871696928'
            ])
        );
        $response = $createPost->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"create":"done","uuid":"f9cdfe1c-1a03-4786-89a4-f4a871696928"}}');
        $response->send();
    }

    /**
     * @throws Exception
     */
    function testItReturnsErrorIfRequestContainsInvalidUuid() {

        $createPost = $this->createMock(CreatePost::class);
        $createPost->method('handle')->willReturn(
            new ErrorResponse('Malformed UUID: 123')
        );

        $request = new Request([],[], '{"author_uuid":"123"}');

        $response = $createPost->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: 123"}');
        $response->send();

    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    function testItReturnsErrorIfUserNotFoundByUuid() {

        $postRepository = $this->createStub(SqlitePostRepository::class);
        $userRepository = $this->userRepository();
        $createPost = new CreatePost($postRepository, $userRepository);
        $request = new Request([],[],'{"author_uuid":"86a34c9e-623d-4058-ae0e-a354aafe9e66"}');

        $response = $createPost->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"User not found: uuid [86a34c9e-623d-4058-ae0e-a354aafe9e66]"}');
        $response->send();
    }

    public static function argumentsProvider(): iterable {
        return [
            ['{"author_uuid":"c170cafd-4f55-4658-80ce-bedc0b620a8d","title":"title","text":" "}', '{"success":false,"reason":"Empty field: text"}'],
            ['{"author_uuid":"c170cafd-4f55-4658-80ce-bedc0b620a55","title":"title"," ":"text"}', '{"success":false,"reason":"No such field: text"}']
        ];
    }

    /**
     * @dataProvider argumentsProvider
     * @throws Exception
     */
    public function testItReturnsErrorIfRequestDoseNotContainEnoughDataForCreatePost($inputValue, $expectedValue): void {
        $postRepository = $this->createStub(SqlitePostRepository::class);
        $userRepository = $this->userRepository2([
            new User(
                new UUID('c170cafd-4f55-4658-80ce-bedc0b620a8d'),
                new Name('Nikolay', 'Nikitin'),
                'koliy',
            ),
            new User(
                new UUID('c170cafd-4f55-4658-80ce-bedc0b620a55'),
                new Name('Ivan', 'Nikitin'),
                'ivan',
            ),
        ]);
        $createPost = new CreatePost($postRepository,$userRepository);
        $request = new Request([],[], $inputValue);

        $response = $createPost->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString($expectedValue);
        $response->send();

    }


    private function userRepository()
    {

        return new class implements UsersRepositoryInterface {

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("User not found: uuid [86a34c9e-623d-4058-ae0e-a354aafe9e66]");
            }

            public function getByUsername(string $username): User
            {
                return new User(
                    new UUID('e1b3db34-f69f-4425-bbfb-d437ed08a0a1'),
                    new Name('Leo', 'Petrov'),
                    'leo222'
                );
            }

        };

    }
    private function userRepository2($users) : UsersRepositoryInterface
    {

        return new class($users) implements UsersRepositoryInterface {

            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if($user->uuid() == $uuid) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("User not found: uuid [$uuid]");
            }

            public function getByUsername(string $username): User
            {
                return new User(
                    new UUID('e1b3db34-f69f-4425-bbfb-d437ed08a0a1'),
                    new Name('Leo', 'Petrov'),
                    'leo222'
                );
            }

        };

    }
//● класс возвращает успешный ответ;
//● класс возвращает ошибку, если запрос содержит UUID в неверном формате;
//● класс возвращает ошибку, если пользователь не найден по этому UUID;
//● класс возвращает ошибку, если запрос не содержит всех данных, необходимых для
//создания статьи.

}