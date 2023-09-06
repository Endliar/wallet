<?php

namespace App\Http\Repository;

use App\Http\Request\AuthUserRequest;
use App\Http\Request\CreateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use function Laravel\Prompts\error;

class UserRepository
{
    public function create(CreateUserRequest $request): User
    {
        $model = new User();
        $model->name = $request['name'];
        $model->email = $request['email'];
        $model->password = Hash::make($request['password']);
        $model->save();

        return $model;
    }

    public function auth(AuthUserRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['Error' => 'Пользователь не найден'], 401);
            }
            $token = JWTAuth::fromUser($user);

            return response()->json(['accessToken' => $token], 200);
        }
        return response()->json(['Error' => 'Неверный результат'], 401);
    }

    public function searchToEmail(AuthUserRequest $request)
    {
        return User::where('email', $request['email'])->first();
    }

    public function isPassword(AuthUserRequest $request)
    {
        $model = User::where('email', $request['email'])->first();
        return Hash::check($request['password'], $model->password,) ? $model : null;
    }
}
