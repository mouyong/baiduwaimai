<?php

namespace App\Traits;

trait Http
{
    public $client;
    public function client($url)
    {
//        $userAgent = $userAgent ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';
        $userAgent = first_no_null($userAgent, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');

        $this->client = new \GuzzleHttp\Client([
            'headers' => [
                'User-Agent' => $userAgent
            ],
            'base_uri' => $url
        ]);
        return $this;
    }

    public function send($option, $url = '')
    {
        if (!$this->api_url) {
            $this->api_url($url);
        }

        $response = $this->client->post($this->api_url, [
            'form_params' => $option
        ]);
        $response = json_decode($response->getBody(), true);

        return $response;
    }
}