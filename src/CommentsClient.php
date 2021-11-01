<?php

namespace CommentHTTPClient;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use CommentHTTPClient\ResponseData;
use CommentHTTPClient\Entity\CommentEntity;

class CommentsClient
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
        
    /**
     * Получение комментариев из источника
     *
     * @return array
     */
    public function getComments(): array
    {
        try {
            $response = $this->client->get('comments');
        } catch (\Exception $e) {
            // В реальном проекте это должно быть обработано
            // в тестовом задании думаю перевыбросить исключение достаточно
            throw $e;
        }

        return ResponseData::parseCommentsFromResponse($response);
    }
    
    /**
     * Добавление комментария
     *
     * @param  mixed $name
     * @param  mixed $text
     * @return void
     */
    public function addComment(string $name, string $text): CommentEntity
    {
        try {
            $newComment = [
                'name'  => $name,
                'text'  => $text
            ];
            $response = $this->client->post('comment', [
                RequestOptions::JSON => $newComment
            ]);

            if ($response->getStatusCode() === 200) {
                // пусть сервер аналогично возвращает json в котором id добавленной записи
                $result = json_decode($response->getBody(), true);
                if (!empty($result['id'])) {
                    $newComment['id'] = $result['id'];
                    return CommentEntity::createFromRaw($newComment);
                } else {
                    // сейчас получится не очень красиво с перевыбросом исключения ниже
                    // но в теории на настоящей задаче блок catch как-то обрабатывается
                    throw new \Exception('No ID from comments backend, something wrong ..');
                }
            }
        } catch (\Exception $e) {
            // см выше
            throw $e;
        }
    }
    
    /**
     * Обновление комментария
     *
     * @param  mixed $comment
     * @return bool
     */
    public function updateComment(CommentEntity $comment): bool
    {
        try {
            $response = $this->client->put('comment/' . $comment->id, [
                RequestOptions::JSON => ['name' => $comment->name , 'text' => $comment->text]
            ]);

            // Согласно MDN успешный PUT возвращает 201
            if ($response->getStatusCode() === 201) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            // см выше
            throw $e;
        }
    }
}