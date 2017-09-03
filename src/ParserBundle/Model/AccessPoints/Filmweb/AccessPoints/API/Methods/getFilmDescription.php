<?php

namespace AccessPoints\API\Methods;
final class getFilmDescription extends \AccessPoints\API\Methods
{
    // Nazwa metody
    public $method = 'getFilmDescription';
    
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
        'description'
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => $this->filmId
        ];        
    }
}