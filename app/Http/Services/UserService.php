<?php

namespace App\Http\Services;

use App\Http\Repository\UserRepository;
use App\Http\Request\AuthUserRequest;
use App\Http\Request\CreateUserRequest;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;

class UserService
{
    protected UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function login(AuthUserRequest $context): JsonResponse| ResponseFactory
    {
        if ($user = $this->repository->searchToEmail($context)) {
            if ($this->repository->isPassword($context)) {
                return $this->repository->auth($context, $user);
            } else {
                return response('Пароль неверен');
            }
        } else {
            return response('Такого пользователя не существует');
        }
    }

    public function register(CreateUserRequest $context): User
    {
        return $this->repository->create($context);
    }
}
