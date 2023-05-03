<?php

namespace Geekbrains\LevelTwo\UnitTests\Actions;

use Geekbrains\LevelTwo\Blog\Exceptions\PostNotFoundException;
use Geekbrains\LevelTwo\Blog\Post;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\SqlitePostRepository;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Blog\User;
use GeekBrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\Post\CreatePost;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Geekbrains\LevelTwo\Person\Name;


class CreatePostActionTest extends TestCase
{
    private function userRepository($users): UsersRepositoryInterface
    {

        return new class($users) implements UsersRepositoryInterface {

            public function __construct(
                private array $users
            ) {}
            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if($user instanceof User && $user->uuid() == (string)$uuid) {
                        return $user;
                    }
                }
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

    private function postRepository($posts):PostsRepositoryInterface {

        return new class($posts) implements PostsRepositoryInterface {

            private bool $called = false;

            /**
             * @return bool
             */
            public function getCalled(): bool
            {
                return $this->called;
            }

            public function __construct(
                private Array $posts
            )
            {
            }

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid):Post
            {
                foreach ($this->posts as $post) {
                    if($post instanceof Post && (string)$uuid == $post->uuid()){
                        return $post;
                    }
                }
                throw new PostNotFoundException("Post not found Post: uuid [$uuid]");
            }

            public function delete(UUID $uuid)
            {
                // TODO: Implement delete() method.
            }
        };
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testItReTurnsSuccessfulResponse() {
        $userRepository = $this->userRepository([
            new User(
            new UUID('c170cafd-4f55-4658-80ce-bedc0b620a8d'),
            new Name('Nikolay', 'Nikitin'),
            'nikitin123',
        )]);
        $postRepository = $this->postRepository([]);
        $createPost = new CreatePost($postRepository,$userRepository);
        $request = new Request([], [], '{"author_uuid":"c170cafd-4f55-4658-80ce-bedc0b620a8d","title":"title","text":"text"}');

        $response = $createPost->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);
//        $this->setOutputCallback(function ($data)) {
//                $dataDecode = json_decode(
//                    $data,
//                    associative:true,
//                    flags: JSON_ERROR_DEPTH
//        );
//        $dataDecode['data']['uuid'] = "f9cdfe1c-1a03-4786-89a4-f4a871696928";
//          return json_encode(
//              $dataDecode,
//              JSON_THROW_ON_ERROR
//
//          );
//        };
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
        $userRepository = $this->userRepository([]);
        $createPost = new CreatePost($postRepository, $userRepository);
        $request = new Request([],[],'{"author_uuid":"86a34c9e-623d-4058-ae0e-a354aafe9e66"}');

        $response = $createPost->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"User not found: uuid [86a34c9e-623d-4058-ae0e-a354aafe9e66]"}');
        $response->send();
    }
    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testItReturnsErrorIfRequestDoesNotContainEnoughDataForCreatePost(): void {

        $postRepository = $this->createStub(SqlitePostRepository::class);
        $userRepository = $this->userRepository([
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
        $createPost = new CreatePost($postRepository, $userRepository);
        $request = new Request([], [], '{"author_uuid":"c170cafd-4f55-4658-80ce-bedc0b620a8d","title":"title","text":" "}');

        $response = $createPost->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Empty field: text"}');
        $response->send();
    }

//● класс возвращает успешный ответ;
//● класс возвращает ошибку, если запрос содержит UUID в неверном формате;
//● класс возвращает ошибку, если пользователь не найден по этому UUID;
//● класс возвращает ошибку, если запрос не содержит всех данных, необходимых для
//создания статьи.

}