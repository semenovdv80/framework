<?php

namespace Essential\Database;

use PDO;
use phpDocumentor\Reflection\Types\Self_;

class Model
{
    protected static $connect;
    protected static $modelTable;
    protected static $columns = [];
    protected static $vars = [];
    protected static $values = [];

    /**
     * Get conection and table
     *
     * @throws \ReflectionException
     */
    public static function init()
    {
        self::$connect = Connection::getConnection();
        self::$modelTable = self::getTableName(static::class);
    }


    /**
     * Get name of class
     *
     * @param $class
     * @return string
     * @throws \ReflectionException
     */
    protected static function getClassName($class)
    {
        return (new \ReflectionClass($class))->getShortName();
    }

    /**
     * Get table name
     *
     * @param $class
     * @return string
     * @throws \ReflectionException
     */
    public static function getTableName($class)
    {
        $properties = get_class_vars($class);
        return $properties['table'] ?? strtolower(self::getClassName($class) . 's');
    }

    /**
     * Create new record
     *
     * @param array $attributes
     */
    public static function create($attributes = [])
    {
        try {
            self::init();

            foreach ($attributes as $column => $value) {
                self::$columns[] = $column;
                self::$vars[] = '?';
                self::$values[] = $value;
            }

            $sql = 'INSERT INTO ' . self::$modelTable . ' (' . implode(',', self::$columns) . ') VALUES (' . implode(',', self::$vars) . ')';
            $stm = self::$connect->prepare($sql);
            $stm->execute(self::$values);
            return self::$connect->lastInsertId();
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Select specified columns from table
     * 
     * @return mixed
     */
    public static function select()
    {
        try {
            self::init();
            $args = func_get_args();
            foreach ($args as $column) {
                self::$columns[] = $column;
            }

            $query = 'SELECT ' . implode(', ', self::$columns) . ' FROM ' . self::$modelTable;
            return new QueryBuilder(self::$connect, $query);

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Get all records
     *
     * @return mixed
     */
    public static function all()
    {
        try {
            self::init();
            $query = 'SELECT * FROM ' . self::$modelTable;
            $qb = new QueryBuilder(self::$connect, $query);
            return $qb->get();

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Where statement
     *
     * @param $column
     * @param $value
     * @return QueryBuilder
     */
    public static function where($column, $value)
    {
        try {
            self::init();
            $query = 'SELECT * FROM ' . self::$modelTable . ' WHERE '. $column. ' = '. $value;
            return new QueryBuilder(self::$connect, $query);

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Order by
     *
     * @param string $col
     * @param string $type
     * @return $this
     */
    public static function orderBy($col = 'id', $type='asc')
    {
        try {
            self::init();
            $query = 'SELECT * FROM ' . self::$modelTable . ' ORDER BY '.$col. ' '.$type;
            return new QueryBuilder(self::$connect, $query);

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }
}
