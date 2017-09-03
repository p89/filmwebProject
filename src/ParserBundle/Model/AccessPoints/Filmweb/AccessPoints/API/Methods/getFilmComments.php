<?php

namespace AccessPoints\API\Methods;
final class getFilmComments extends \AccessPoints\API\Methods
{
    // Nazwa metody
    public $method = 'getFilmComments';
    
   /**
    * Wymagane parametry
    * @var array 
    */
    protected $_args =
    [
        'filmId',
        'pageNo'
    ];
    
   /**
    * Dane zwrócone przez filmweba
    */
    protected $_response_keys =
    [
        'authorUserId', 'authorName', 'authorImagePath', 'rate', 'commentText'
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => $this->filmId . ','  . (5 * $this->pageNo) . ',' . 5 * ($this->pageNo + 1)
        ];        
    }
    
    protected function getData($response)
    {
        $data = [];
        $key = $this->_response_keys[0];        
        $i = 0;
        
        foreach($response as $item)
        {
            $i = new \stdClass;
            
            foreach($this->_response_keys as $k => $v)
            {
                if($v === 'authorImagePath' AND ! is_null($item[$k]))
                {
                    $item[$k] = \AccessPoints\Filmweb::$_config['userImageUrl'] . $item[$k];
                }
                
                $i->$v = $item[$k];
            }
            
            $data[] = $i;
        }
        
        return (object) $data;
    }
}