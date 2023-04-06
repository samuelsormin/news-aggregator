<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NewsApi
{

    public static function request($endpoint)
    {
        $fullpath = sprintf('%s/%s&apiKey=%s', env('NEWSAPI_BASE_URL'), $endpoint, env('NEWSAPI_APIKEY'));

        try {
            $client = new Client();
            $response = $client->request('GET', $fullpath);

            $object_response = json_decode($response->getBody()->getContents());

            return $object_response;
            //
        } catch (\Throwable $th) {
            Log::error('Failed to fetch data from NewsApi.', [
                'url' => $fullpath,
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return FALSE;
        }
    }
}
