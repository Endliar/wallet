<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetCurrencyController extends Controller
{
    public function index(): ResponseFactory
    {
        return response('Просто текст') -> header('content-type', 'text/plain');
    }

    public function getCurrency(string $date_start)
    {
        $userId = auth()->id();

        $token = request()->header('Authorization');
        if (!$token) {
            return response('Отсутствует токен авторизации', 401)->header('Content-Type', 'text/plain');
        }

        $user = auth()->user();
        if (!$user) {
            return response('Пользователь не найден', 401)->header('Content-Type', 'text/plain');
        }

        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return response('Токен устарел или не действителен', 401)->header('content-type', 'text/plain');
        }

        $url = 'https://cbr.ru/scripts/XML_daily.asp?date_req='.urlencode($date_start);
        $token = JWTAuth::fromUser($user);

        $response = Http::withHeaders([
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
                'user-id' => $userId
            ]
        ])->withOptions(['verify' => false])->get($url);

        $xml = simplexml_load_string($response->body());

        $filtered_currencies = [];
        foreach ($xml->Valute as $valute) {
            $currency_code = (string) $valute->CharCode;
            if (in_array($currency_code, ['USD', 'EUR', 'CNY'], true)) {
                $value = str_replace(',', '.', $valute->Value);
                $valute_arr = [
                    'NumCode' => (string) $valute->NumCode,
                    'CharCode' => $currency_code,
                    'Nominal' => (int) $valute->Nominal,
                    'Name' => (string) $valute->Name,
                    'Value' => floatval($value),
                ];
                $filtered_currencies[$currency_code] = $valute_arr;
            }
        }

        return response()->json($filtered_currencies, 200);
    }
}
