<?php

namespace CommentHTTPClient\Entity;

class CommentEntity
{    
    /**
     * id
     *
     * @var int
     */
    public $id = 0;
        
    /**
     * name
     *
     * @var string
     */
    public $name = '';
        
    /**
     * text
     *
     * @var string
     */
    public $text = '';
    
    /**
     * Создает экземпляр CommentEntity из массива
     *
     * @param  mixed $data
     * @return CommentEntity
     */
    public static function createFromRaw(array $data): CommentEntity
    {
        $e = new CommentEntity;
        $e->id = $data['id'];
        $e->name = $data['name'];
        $e->text = $data['text'];

        return $e;
    }
}