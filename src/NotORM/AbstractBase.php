<?php


namespace NotORM;

abstract class AbstractBase
{
    /**
     * @var \PDO
     */
    protected $connection;
    /**
     * @var string Driver name
     */
    protected $driver;
    /**
     * @var Structure
     */
    protected $structure;
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var NotORM
     */
    protected $notORM;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var string
     */
    protected $primary;
    /**
     * @var array|null
     */
    protected $rows;
    /**
     * @var array
     */
    protected $referenced = array();

    /**
     * @var bool
     */
    protected $debug = false;
    /**
     * @var bool
     */
    protected $freeze = false;
    /**
     * @var string
     */
    protected $rowClass = '\\NotORM\\Row';
    /**
     * @var bool
     */
    protected $jsonAsArray = false;

    protected function access($key, $delete = false)
    {
    }
}