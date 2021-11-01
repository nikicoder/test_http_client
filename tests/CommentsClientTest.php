<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use CommentHTTPClient\HttpClient;
use CommentHTTPClient\CommentsClient;
use CommentHTTPClient\Entity\CommentEntity;

class CommentsClientTest extends TestCase
{
    public function testFetchComments()
    {
        $data = [
            ['id' => 1, 'name' => 'Первый', 'text' => 'Первый комментарий'],
            ['id' => 2, 'name' => 'Второй', 'text' => 'Второй комментарий']
        ];

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($data)),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = HttpClient::init([
            'handler'   => $handlerStack
        ]);

        $result = [];
        foreach($data as $d) {
            $result[] = CommentEntity::createFromRaw($d);
        }
        
        $this->assertEquals($result, (new CommentsClient($client))->getComments());
    }

    public function testFetchHttpError()
    {
        $this->expectException(\Exception::class);

        $mock = new MockHandler([
            new Response(403, [], 'Some wrong'),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = HttpClient::init([
            'handler'   => $handlerStack
        ]);

        $result = (new CommentsClient($client))->getComments();
    }

    public function testPostComment()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['id' => 3])),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = HttpClient::init([
            'handler'   => $handlerStack
        ]);

        $name = 'Трерий';
        $text = 'Третий комментарий';

        $resComment = [
            'id'    => 3,
            'name'  => $name,
            'text'  => $text
        ];

        $result = CommentEntity::createFromRaw($resComment);

        $this->assertEquals($result, (new CommentsClient($client))->addComment($name, $text));
    }

    public function testPostCommentError()
    {
        $this->expectException(\Exception::class);

        $mock = new MockHandler([
            new Response(200, [], json_encode(['Some error on server backend'])),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = HttpClient::init([
            'handler'   => $handlerStack
        ]);

        $name = 'Трерий';
        $text = 'Третий комментарий';

        $result = (new CommentsClient($client))->addComment($name, $text);
    }

    public function testPostCommentUpdate()
    {
        $mock = new MockHandler([
            new Response(201, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = HttpClient::init([
            'handler'   => $handlerStack
        ]);

        $upComment = [
            'id'    => 3,
            'name'  => 'Трерий обновлено',
            'text'  => 'Третий комментарий обновлено'
        ];

        $this->assertTrue((new CommentsClient($client))
            ->updateComment(CommentEntity::createFromRaw($upComment)));
    }
}