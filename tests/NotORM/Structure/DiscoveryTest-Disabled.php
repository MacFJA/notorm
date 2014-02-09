<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 04/02/14
 * Time: 22:02
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM\Structure;

use NotORM\NotORMBaseTestCase;

class DiscoveryTest extends NotORMBaseTestCase
{
    protected static $discovery;

    public static function setUpBeforeClass()
    {
        /*$connection = new PDO("sqlite::memory:");
        $connection->exec(file_get_contents(__DIR__ . '/../software.sql'));
        self::$discovery = new NotORM($connection, new Discovery($connection));*/
    }

    /**
     * 8-discovery.phpt
     *
     * Disabled because it's a MySQL feature
     */
    public function testDiscovery()
    {
        $discovery = self::newDatabase(new Discovery(self::getPDO()));
        $expected  = array(
            array('title' => 'Adminer', 'author' => 'Jakub Vrana', 'tags' => array('PHP', 'MySQL')),
            array('title' => 'JUSH', 'author' => 'Jakub Vrana', 'tags' => array('JavaScript')),
            array('title' => 'Nette', 'author' => 'David Grudl', 'tags' => array('PHP')),
            array('title' => 'Dibi', 'author' => 'David Grudl', 'tags' => array('PHP', 'MySQL')),
        );

        foreach (self::$discovery->application() as $application) {
            $line = array_shift($expected);
            $this->assertEquals($line['title'], $application['title']);
            $this->assertEquals($line['author'], $application->author['name']);
            foreach ($application->application_tag() as $application_tag) {
                $this->assertEquals(array_shift($line['tags']), $application_tag->tag['name']);
            }
        }
    }
}
