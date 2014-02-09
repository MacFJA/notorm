<?php


namespace NotORM\Cache;

use NotORM\Cache;

/**
 * Cache using file
 */
class File implements Cache
{
    private $filename, $data = array();

    function __construct($filename)
    {
        $this->filename = $filename;
        if (file_exists($filename)) {
            $this->data = unserialize(file_get_contents($filename));
        }
    }

    function load($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    function save($key, $data)
    {
        if (!isset($this->data[$key]) || $this->data[$key] !== $data) {
            $this->data[$key] = $data;
            file_put_contents($this->filename, serialize($this->data), LOCK_EX);
        }
    }
}