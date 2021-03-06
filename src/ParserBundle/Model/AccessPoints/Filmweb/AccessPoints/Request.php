<?php

namespace AccessPoints;

class Request
{
    public static $default_options =
        [
            CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A334 Safari/7534.48.3',
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE
        ];

    public static function execute($params, array $options = NULL)
    {
        if ($options === NULL)
            $options = \AccessPoints\Request::$default_options;
        else
            $options = $options + \AccessPoints\Request::$default_options;

        $request = curl_init(\AccessPoints\Filmweb::API_SERVER.http_build_query($params));

        if ( ! curl_setopt_array($request, $options))
            throw new \Exception('Failed to set CURL options, check CURL documentation: http://php.net/curl_setopt_array');

        $response = curl_exec($request);
        curl_close($request);

        return $response;
    }
}