<?php

namespace AccessPoints\API\Methods;
final class getFilmPersons extends \AccessPoints\API\Methods
{
    // Nazwa metody
    public $method = 'getFilmPersons';
    
   /**
    * Wymagane parametry
    * @var array 
    */
    protected $_args =
    [
        'filmId',
        'type',
        'pageNo',
    ];
    
   /**
    * Dane zwrócone przez filmweba
    */
    protected $_response_keys =
    [
        'personId', 'assocName', 'assocAttributes', 'personName', 'personPhoto'
    ];
    
   /**
    * Callbacki
    */
    protected $_functions =
    [
        'cats' => ['explode', ',']
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => $this->filmId . ',' . $this->type . ',' . (50 * $this->pageNo) . ',' . 50 * ($this->pageNo + 1)
        ];        
    }
    
    protected function getData($response)
    {
        $data = [];
        $key = $this->_response_keys[0];
        $type = \AccessPoints\Filmweb::$roles[$this->type];
        
        $i = 0;
        
        foreach($response as $item)
        {
            $i = new \stdClass;
            
            foreach($this->_response_keys as $k => $v)
            {
                if($v === 'personPhoto' AND ! is_null($item[$k]))
                {
                    $item[$k] = \AccessPoints\Filmweb::$_config['personImageUrl'] . $item[$k];
                }
                
                $i->$v = $item[$k];
            }
            
            $data[] = $i;
        }
        
        return (object) $data;
    }
}