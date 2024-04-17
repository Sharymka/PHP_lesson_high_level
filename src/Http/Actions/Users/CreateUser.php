<?php

namespace Geekbrains\LevelTwo\Http\Actions\Users;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\HttpException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserAlreadyExistException;
use Geekbrains\LevelTwo\Blog\Repositories\PostRepositories\PostsRepositoryInterface;
use Geekbrains\LevelTwo\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Geekbrains\LevelTwo\Blog\User;
use Geekbrains\LevelTwo\Blog\UUID;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\Auth\PasswordAuthentication;
use Geekbrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;
use Geekbrains\LevelTwo\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PasswordAuthentication $authentication,
        private LoggerInterface $logger
    )
    {
    }

    public function handle(Request $request): Response
    {

        if($this->authentication->user($request)) {
            throw new UserAlreadyExistException("User already exist");
        }

       try{
           $newUserUuid = UUID::random();
           $user =  User::createFrom(
               $request->jsonBodyField("username"),
               $request->jsonBodyField("password"),
               new Name($request->jsonBodyField("first_name"), $request->jsonBodyField("last_name")),
           );
       } catch(HttpException $e){
            return new ErrorResponse($e->getMessage());
       }

       $this->usersRepository->save($user);

       $this->logger->info("User created: uuid[$newUserUuid]");

       return new SuccessfulResponse([
           'uuid'=> (string)$newUserUuid,
       ]);
    }
}