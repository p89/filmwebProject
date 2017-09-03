<?php

namespace AccessPoints\API\Methods;
final class Login extends \AccessPoints\API\Methods
{
    // Nazwa metody
    protected $method = 'login';
    protected $post = TRUE;

   /**
    * Wymagane parametry
    * @var array 
    */
    protected $_args =
    [
        'login',
        'password',
    ];
    
   /**
    * Dane zwrócone przez filmweba
    */
    protected $_response_keys =
    [
        'username',
        'dont_know',
        'name',
        'user_id',
        'gender'
    ];
    
    protected function prepare()
    {
        $this->methods = [
            $this->method => '"'.$this->login.'"' . ', ' . '"'.$this->password.'", 1'
        ];        
    }
}