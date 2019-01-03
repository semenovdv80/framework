<?php

namespace Essential\Database;

use Essential\Http\Request;

class QueryBuilder
{
    protected $connect;
    protected $query;

    /**
     * QueryBuilder constructor.
     *
     * @param $query
     */
    public function __construct($connect, $query)
    {
        $this->connect = $connect;
        $this->query = $query;
    }

    /**
     * Get all records
     *
     * @return mixed
     */
    public function get()
    {
        try {
            if (!is_null($this->query)) {
                $statement = $this->connect->prepare($this->query);
                $statement->execute();
                return $statement->fetchAll();
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * All records
     *
     * @return mixed
     */
    public function all()
    {
        try {
            if (!is_null($this->query)) {
                $statement = $this->connect->prepare($this->query);
                $statement->execute();
                return $statement->fetchAll();
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Order by statement
     *
     * @param string $col
     * @param string $type
     * @return $this
     */
    public function orderBy($col = 'id', $type='asc')
    {
        try {
            if (!is_null($this->query)) {
                $this->query .= ' ORDER BY '.$col. ' '.$type;
                return $this;
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }


    /**
     * Pagianate records
     *
     * @param $limit
     * @return mixed
     */
    public function paginate($limit)
    {
        try {
            if (!is_null($this->query)) {

                $limit = request()->limit ?? $limit;
                $offset = request()->offset ?? 0;

                $this->query .= ' LIMIT '.$limit. ' OFFSET '.$offset;

                $statement = $this->connect->prepare($this->query);
                $statement->execute();
                return $statement->fetchAll();
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Like query
     *
     * @param $column
     * @param $value
     * @return $this
     */
    public function whereLike($column, $value)
    {
        try {
            if (!is_null($this->query)) {
                $this->query .= ' AND '.$column. ' LIKE "'.$value.'"';
                return $this;
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Or where like statement
     *
     * @param $column
     * @param $value
     * @return $this
     */
    public function orWhereLike($column, $value)
    {
        try {
            if (!is_null($this->query)) {
                $this->query .= ' OR '.$column. ' LIKE "'.$value.'"';
                return $this;
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Where statement
     *
     * @param $column
     * @param null $value
     * @return $this
     */
    public function where($column, $value=null)
    {
        try {
            if (!is_null($this->query)) {
                if($column instanceof \Closure) {
                    $this->groupWhere($column);
                } else {
                    $this->query .= ' AND '.$column. ' = "'.$value.'"';
                }
                return $this;
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Or where statement
     *
     * @param $column
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $value=null)
    {
        try {
            if (!is_null($this->query)) {
                if($column instanceof \Closure) {
                    $this->groupWhere($column);
                } else {
                    $this->query .= ' OR '.$column. ' = "'.$value.'"';
                }
                return $this;
            }

        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Group where statement
     *
     * @param $callback
     */
    public function groupWhere($callback)
    {
        $origQuery = $this->query; //get original raw of query
        $this->query = ''; //clear orig part in order to get only group part
        $qb = $callback($this); //get queryBuilder from call back
        $query = $qb->query.')'; //get group part of query from queryBuilder with )
        $query = preg_replace('/AND /', 'AND (', $query, 1); //add (
        $this->query = $origQuery.$query; //make full query
    }
}
