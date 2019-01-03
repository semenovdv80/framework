<?php

namespace Essential\Http;

class Request
{
    private static $request;
    private $method;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setRequestAttributes();
        self::$request = $this;
    }

    /**
     Check request method
     *
     * @param $method
     * @return bool
     */
    public function isMethod($method){
        return $this->method == $method ? true : false;
    }

    /**
     *
     */
    public function setRequestAttributes()
    {
        foreach ($_REQUEST as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $_REQUEST;
    }

    /**
     * Get current request
     *
     * @return $this
     */
    public static function getCurrent()
    {
        if (is_null(self::$request)) {
            self::$request = new self();
        }
        return self::$request;
    }
}