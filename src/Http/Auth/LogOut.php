<?php

namespace Geekbrains\LevelTwo\Http\Auth;

use Geekbrains\LevelTwo\Blog\Exceptions\AuthException;
use Geekbrains\LevelTwo\Blog\Exceptions\AuthTokensRepositoryException;
use Geekbrains\LevelTwo\Blog\Exceptions\UserNotFoundException;
use Geekbrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Geekbrains\LevelTwo\Http\Actions\ActionInterface;
use Geekbrains\LevelTwo\Http\ErrorResponse;
use Geekbrains\LevelTwo\Http\Request;
use Geekbrains\LevelTwo\Http\Response;
use DateTimeImmutable;
use Geekbrains\LevelTwo\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{

    public function __construct(
        private TokenAuthenticationInterface $authentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try{
            $authToken = $this->authentication->getToken($request);
        }catch(AuthException $e) {
            return  new ErrorResponse($e->getMessage());
        }

        $authToken->setExpiresOn(new DateTimeImmutable());

        try {
            $this->authTokensRepository->save($authToken);
        } catch (AuthTokensRepositoryException $e) {
            return  new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            "logOut" => "true",
            "token" => $authToken->token()
        ]);
    }
}