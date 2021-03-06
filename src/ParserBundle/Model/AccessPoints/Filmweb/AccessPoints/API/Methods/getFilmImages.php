<?php

namespace AccessPoints\API\Methods;
final class getFilmImages extends \AccessPoints\API\Methods
{
    // Nazwa metody
    public $method = 'getFilmImages';
    
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
        'imagePath', 'persons', 'photoSources'
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => $this->filmId . ','  . (100 * $this->pageNo) . ',' . 100 * ($this->pageNo + 1)
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
                if($v === 'imagePath' AND ! is_null($item[$k]))
                {
                    $item[$k] = \nSolutions\Filmweb::$_config['filmPhotoUrl'] . strtr($item[$k], ['.0.jpg' => '.3.jpg']);
                }
                
                if($v === 'persons')
                {
                    $persons = [];
                    
                    foreach($item[$k] as $person)
                    {
                        $persons[] = ['personId' => $person[0], 'personName' => $person[1]];
                    }
                    
                    $i->$v = $persons;
                }
                else
                {
                    $i->$v = $item[$k];
                }
            }
            
            $data[] = $i;
        }
        
        return (object) $data;
    }
}