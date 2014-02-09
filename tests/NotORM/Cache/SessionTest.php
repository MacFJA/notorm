<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 04/02/14
 * Time: 22:36
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM\Cache;

use NotORM\NotORMBaseTestCase;

class SessionTest extends CacheShareFunction
{


    /**
     * 9-cache.phpt
     *
     * @test
     */
    public function testCache()
    {
        $cache = self::newDatabase(null, new Session());
        $this->sharedCode($cache);
        self::unloadDatabase();
    }
}
