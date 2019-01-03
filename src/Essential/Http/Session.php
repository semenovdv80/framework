<?php

namespace Essential\Http;

class Session
{
    private static $session;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->setSessionAttributes();
        self::$session = $this;
    }

    /**
     *
     */
    public function setSessionAttributes()
    {
        foreach ($_SESSION as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $_SESSION;
    }

    /**
     * Get current session
     *
     * @return $this
     */
    public static function getCurrent()
    {
        if (is_null(self::$session)) {
            self::$session = new self();
        }
        return self::$session;
    }

    /**
     * Set value
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $this->$key = $value;
    }

    /**
     * Unset key
     *
     * @param $key
     */
    public function unset($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            unset($this->$key);
        }
    }

    /**
     * Unset result message
     */
    public function unflash()
    {
        $this->unset('result');
    }

    /**
     * Check if key exists
     *
     * @param $key
     * @return bool
     */
    public function has($key, $value = null)
    {
        if (is_null($value)) {
            return isset($_SESSION[$key]) ? true : false;
        } else {
            return isset($_SESSION[$key]) && $_SESSION[$key] == $value ? true : false;
        }
    }
}