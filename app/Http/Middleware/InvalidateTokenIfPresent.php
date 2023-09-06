<?php
//
//namespace App\Http\Middleware;
//
//use Closure;
//use http\Env\Response;
//use Tymon\JWTAuth\Facades\JWTAuth;
//
//class InvalidateTokenIfPresent
//{
//    public function handle($request, Closure $next) {
//        $token = JWTAuth::getToken();
//
//        if ($token) {
//            try {
//                JWTAuth::invalidate($token);
//            } catch (\Exception $e) {
//                throw new \Exception("Не удалось удалить токен $e");
//            }
//        }
//        return $next($request);
//    }
//}
