<?php

namespace AccessPoints\API;

class Methods
{
    protected $method = NULL;
    protected $_args = [];
    protected $method_query, $signature;
    
    protected $post = FALSE;

   /**
    * @param array $args
    * @throws \Exception
    */
    public function __construct($args)
    {
        if(count($this->_args) > count($args))
            throw new \Exception ('Nie podano wszystkich wymaganych argumentów.');

        foreach($this->_args as $k => $v)
        {
            $this->$v = $args[$k];
        }
    }
    
    public function execute()
    {
        $this->prepare();
        return $this->_process();
    }

   /**
    * @param object $response
    * @return \stdClass
    */
    protected function parse($response)
    {
        if(!$response) {
            return FALSE;
        } else {
            $response = explode("\n", $response);


            if($response[1] == 'exc NullPointerException' || $response[1] == 'null')
                return FALSE;

            $response = json_decode(preg_replace('/ t:[0-9]+/i', '', $response[1]));
            return $this->getData($response);
        }

    }
    
    protected function getData($response)
    {
        $data = new \stdClass;
        foreach($response as $k => $v)
        {
            $key = $this->_response_keys[$k];
            if(isset($this->_functions[$key]))
            {
                $function = $this->_functions[$key];
                $data->$key = call_user_func_array($function[0], [$function[1],  $v]);
            }
            else
            {
                $data->$key = $v;
            }
        }
        return $data;
    }

   /**
    * @return string
    */
    protected function _process()
    {
        $method = '';
        
        foreach($this->methods as $m => $v)
        {
            $method .= $m . ' ['.$v.']\n';
        }
        
        $method_query = $method;
        $signature = $this->_createApiSignature($method);
        
        $params = ['methods' => $method_query, 'signature' => $signature, 'version' => '1.0', 'appId' => 'android'];

        if($this->post)
        {
            $string = '';

            foreach($params as $key => $param)
            {
                $string .= $key.'='.$param.'&';
            }

            $response = \AccessPoints\Request::execute(array(), [
                CURLOPT_POST => TRUE,
                CURLOPT_POSTFIELDS => $string
            ]);
        }
        else
        {
            $response = \AccessPoints\Request::execute($params);
        }

        return $this->parse($response);
    }
    
   /**
    * Utworzenie sygnatury
    * @param string $method
    * @return string
    */
    protected function _createApiSignature($method)
    {
        return '1.0,'.md5($method.'android'.\AccessPoints\Filmweb::KEY);
    }
}