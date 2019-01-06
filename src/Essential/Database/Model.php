<?php

namespace Essential\Database;

use PDO;
use phpDocumentor\Reflection\Types\Self_;

class Model
{
    protected static $connect;
    protected static $modelTable;

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
                $columns[] = $column;
                $values[] = ':'.$column;
            }

            $sql = 'INSERT INTO ' . self::$modelTable . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $values) . ')';
            $stm = self::$connect->prepare($sql);
            $stm->execute($attributes);
            return self::find(self::$connect->lastInsertId());
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Update object record
     */
    public function update($attributes)
    {
        try {
            self::init();
            foreach ($attributes as $column => $value) {
                $columns[] = $column. ' = :'.$column;
            }
            $sql = 'UPDATE ' . self::$modelTable . ' SET ' . implode(', ', $columns) . ' WHERE id = ' . $this->id;
            $stm = self::$connect->prepare($sql);
            $stm->execute($attributes);
            $this->setAttributes($attributes);
            return $this;
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
            $columns = func_get_args();
            $query = 'SELECT ' . implode(', ', $columns) . ' FROM ' . self::$modelTable;
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

    /**
     * Get object of record
     */
    public static function find($id)
    {
        self::init();
        $query = 'SELECT * FROM ' . self::$modelTable . ' WHERE id = '.$id;
        $qb = new QueryBuilder(self::$connect, $query);
        $rows = $qb->get();
        $collection = self::makeCollection($rows);
        return !empty($collection) ? array_shift($collection) : null;
    }

    /**
     * Mace collection of objects
     *
     * @param $rows
     * @return array
     */
    public static function makeCollection($rows)
    {
        $collection = [];
        foreach ($rows as $row)
        {
            $obj = new static;
            $collection[] = $obj->setAttributes(get_object_vars($row));
        }
        return $collection;
    }

    /**
     * Set attributes
     *
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $name=>$value)
        {
            $this->$name = $value;
        }
        return $this;
    }

    /**
     * Delete record
     */
    public function delete()
    {
        try {
            self::init();
            $sql = 'DELETE FROM ' . self::$modelTable . ' WHERE id = ' . $this->id;
            $stm = self::$connect->prepare($sql);
            $stm->execute();
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }
}
