<?php

namespace NotORM\Cache;

class FileTest extends CacheShareFunction
{
    /**
     * 9-cache.phpt 2
     *
     * @test
     */
    public function testCache()
    {
        $filename = __DIR__ . '/cache.textcache';
        file_put_contents($filename, '');
        $cache = self::newDatabase(null, new File($filename));
        $this->sharedCode($cache);
        self::unloadDatabase();
        unlink($filename);
    }
}
