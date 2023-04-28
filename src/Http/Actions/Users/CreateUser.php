<?php

namespace Geekbrains\LevelTwo\Http\Actions\Users;

use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Geekbrains\LevelTwo\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
       try{
           $newUserUuid = UUID::random();
           $user = new User(
               $newUserUuid,
               new Name($request->jsonBodyField("first_name"), $request->jsonBodyField("last_name")),
               $request->jsonBodyField("username")
           );
       } catch(HttpException $e){
            return new ErrorResponse($e->getMessage());
       }

       $this->usersRepository->save($user);

       return new SuccessfulResponse([
           'uuid'=> (string)$newUserUuid,
       ]);
    }
}