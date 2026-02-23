<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/whatsapp/webhook', function (Request $request) {
    Log::info('GOWA webhook masuk', [
        'headers' => $request->headers->all(),
        'payload' => $request->all(),
    ]);

    return response()->json(['ok' => true]);
});

Route::post('/wa/test-send', function (Request $request) {
    $data = $request->validate([
        'phone' => ['required', 'string'],   // contoh: 62812xxxxxxx
        'text'  => ['required', 'string'],
    ]);

    $baseUrl  = rtrim(config('services.gowa.base_url'), '/');
    $user     = config('services.gowa.user');
    $pass     = config('services.gowa.pass');
    $deviceId = config('services.gowa.device_id');

    $http = Http::withBasicAuth($user, $pass);

    if ($deviceId) {
        $http = $http->withHeaders(['X-Device-Id' => $deviceId]);
    }

    // Body ini sesuaikan dengan schema GOWA kamu (cek docs openapi.yml di repo GOWA).
    $resp = $http->post($baseUrl . '/send/message', [
        'phone' => str_contains($data['phone'], '@s.whatsapp.net')
            ? $data['phone']
            : $data['phone'] . '@s.whatsapp.net',
        'message' => $data['text'],
    ]);


    return response()->json([
        'status' => $resp->status(),
        'body'   => $resp->json(),
        'raw'    => $resp->body(),
    ], $resp->successful() ? 200 : 500);
});