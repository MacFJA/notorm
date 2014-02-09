<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 03/02/14
 * Time: 20:13
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

use PDO;

class NotORMBaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @type null|NotORM
     */
    private static $software = null;
    /**
     * @type null|PDO
     */
    private static $connexion = null;

    public static function getPDO()
    {
        if (null === self::$connexion) {
            self::$connexion = new PDO("sqlite::memory:");
        }

        return self::$connexion;
    }

    public static function getDatabase()
    {
        if (null === self::$software) {
            self::newDatabase();
        }
        return self::$software;
    }

    public static function newDatabase(Structure $structure = null, Cache $cache = null)
    {
        if (null !== self::$software) {
            self::unloadDatabase();
        }
        self::$software = new NotORM(self::getPDO(), $structure, $cache);
        self::reloadDatabase();
        return self::$software;
    }

    public static function reloadDatabase()
    {
        self::getPDO()->exec(file_get_contents(__DIR__ . '/software.sql'));
    }

    public static function unloadDatabase()
    {
        self::$software  = null;
        self::$connexion = null;
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    protected function tearDown()
    {
        self::reloadDatabase();
        parent::tearDown();
    }
}
