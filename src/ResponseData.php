<?php

namespace CommentHTTPClient;

use GuzzleHttp\Psr7\Response;
use CommentHTTPClient\Entity\CommentEntity;

class ResponseData
{    
    /**
     * Возвращает массив CommentEntity из массива сырых данных
     *
     * @param  mixed $response
     * @return array
     */
    public static function parseCommentsFromResponse(Response $response): array
    {
        $result = [];

        // Ожидается application/json
        $contentType = $response->getHeader('Content-Type');
        if (empty($contentType) || $contentType[0] !== 'application/json') {
            // в боевом проекте это исключение
            // в рамках тестового пустой результат
            return $result;
        }

        $raw = json_decode($response->getBody(), true);
        if (empty($raw)) {
            return $result;
        }

        foreach ($raw as $rd) {
            $result[] = CommentEntity::createFromRaw($rd);
        }
 
        return $result;
    }
}