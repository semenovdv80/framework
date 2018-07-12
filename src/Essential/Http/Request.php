<?php

namespace Essential\Http;

class Request
{
    private $method;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     Check request method
     * @param $method
     * @return bool
     */
    public function isMethod($method){
        return $this->method == $method ? true : false;
    }
}