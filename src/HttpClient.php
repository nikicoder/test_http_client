<?php

namespace CommentHTTPClient;

use GuzzleHttp\Client;

class HttpClient
{
    const DEFAULT_BASE_URI = 'http://example.com/';
    
    /**
     * Инициализация HTTP клиента
     *
     * @param  mixed $options
     * @return Client
     */
    public static function init(array $options): Client
    {
        $clientOptions = [
            'base_uri' == array_key_exists('base_uri', $options) ?
                $options['base_uri'] : HttpClient::DEFAULT_BASE_URI
        ];

        if (array_key_exists('handler', $options)) {
            $clientOptions['handler'] = $options['handler'];
        }

        return new Client($clientOptions);
    }
}