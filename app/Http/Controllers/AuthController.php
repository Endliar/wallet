<?php

namespace App\Http\Controllers;

use App\Http\Request\AuthUserRequest;
use App\Http\Request\CreateUserRequest;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __constructor(): void
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(AuthUserRequest $request, UserService $service): JsonResponse | ResponseFactory
    {
        $request->validated();
        return $service->login($request);
    }

    public function register(CreateUserRequest $request, UserService $service): User
    {
        $request->validated();
        return $service->register($request);
    }

    /*
     * Метод logout сообщает мне якобы о том, что токен он не находит. При этом в постмане
     * при запросе, который у меня в middleware обрабатывается, на получение списка валют -
     * у меня всё выводит просто замечательно. Если он middleware проходит и выводит -
     * это, получается, что токен то есть. Более того, спустя определённое время по истечению
     * срока действия токена, когда он становится не активным, так скажем, запрос на вывод валют не выполняется.
     * Я просто реально не понимаю, как мне к logout'у это привязать.
     * Если это читаете - я понимаб, что тестовое не исполнил в должной степени, да и вообще мало что изменил с прошлого репозитория.
     * Но если есть ответ на этот вопрос - то можете мне его фидбэком отписать? Вот реально уже сколько над этим сижу думаю.
     */

    public function logout(): JsonResponse // короче конкретно это не работает.
    {
        $user = auth()->user();
        $token = JWTAuth::fromUser($user);
        JWTAuth::invalidate($token);
        auth()->logout();
        return response()->json(['message' => 'Пользователь успешно разлогинился']);
    }

    public function refresh(): void
    {
        $this->createNewToken(auth()->refresh());
    }

    public function userProfile(): JsonResponse
    {
        return response() -> json(auth()->user());
    }

    public function createNewToken($token): JsonResponse
    {
        return response() -> json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
