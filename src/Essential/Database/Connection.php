<?php

namespace Essential\Database;

use PDO;

class Connection
{
    protected static $pdo;

    /**
     * Connection to DB
     */
    public static function setConnection()
    {
        $dhost = getenv('DB_HOST');
        $dbase = getenv('DB_BASE');
        $duser = getenv('DB_USER');
        $dpass = getenv('DB_PASS');

        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$pdo = new PDO("mysql:host=$dhost;dbname=$dbase", $duser, $dpass, $opt);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
            exit();
        }
    }


    /**
     * Get current connection object
     */
    public static function getConnection()
    {
        if (is_null(self::$pdo)) {
            self::setConnection();
        }

        return self::$pdo;
    }
}
