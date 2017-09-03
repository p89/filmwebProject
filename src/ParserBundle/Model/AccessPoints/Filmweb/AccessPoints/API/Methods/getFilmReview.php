<?php

namespace AccessPoints\API\Methods;
final class getFilmReview extends \AccessPoints\API\Methods
{
    // Nazwa metody
    public $method = 'getFilmReview';
    
   /**
    * Wymagane parametry
    * @var array 
    */
    protected $_args =
    [
        'filmId'
    ];
    
   /**
    * Dane zwrócone przez filmweba
    */
    protected $_response_keys =
    [
        'authorName',
        'authorUserId',
        'authorImagePath',
        'review',
        'title'
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => $this->filmId
        ];        
    }
}