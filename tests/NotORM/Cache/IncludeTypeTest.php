<?php

namespace NotORM\Cache;

class IncludeTypeTest extends CacheShareFunction
{
    /**
     * 9-cache.phpt 2
     *
     * @test
     */
    public function testCache()
    {
        $filename = __DIR__ . '/cache.phpcache';
        file_put_contents($filename, '');
        $cache = self::newDatabase(null, new IncludeType($filename));
        $this->sharedCode($cache);
        self::unloadDatabase();
        unlink($filename);
    }
}
