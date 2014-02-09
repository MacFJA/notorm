<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 03/02/14
 * Time: 21:11
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

class ResultTest extends NotORMBaseTestCase
{
    /**
     * 6-aggregation.phpt
     *
     * @test
     */
    function testAggregation()
    {
        $expected = array('Adminer' => 2, 'JUSH' => 1, 'Nette' => 1, 'Dibi' => 2);
        $this->assertEquals(4, self::getDatabase()->application()->count("*"));
        foreach (self::getDatabase()->application() as $application) {
            $count = $application->application_tag()->count("*");
            $this->assertEquals($expected[$application['title']], $count);
        }
    }

    /**
     * 10-update.phpt
     *
     * @test
     */
    function testUpdate()
    {
        $id              = 5; // auto_increment is disabled in demo
        $application     = self::getDatabase()->application()->insert(
            array(
                "id"        => $id,
                "author_id" => self::getDatabase()->author[12],
                "title"     => new Literal("'Texy'"),
                "web"       => "",
                "slogan"    => "The best humane Web text generator",
            )
        );
        $application_tag = $application->application_tag()->insert(array("tag_id" => 21));

        // retrieve the really stored value
        $application = self::getDatabase()->application[$id];
        $this->assertEquals('Texy', $application['title']);

        $application["web"] = "http://texy.info/";
        $this->assertEquals(1, $application->update());
        $this->assertEquals("http://texy.info/", self::getDatabase()->application[$id]["web"]);

        self::getDatabase()->application_tag("application_id", 5)->delete(); // foreign keys may be disabled
        $this->assertEquals(1, $application->delete());
        $this->assertEquals(0, count(self::getDatabase()->application("id", $id)));
    }

    /**
     * 11-pairs.phpt
     *
     * @test
     */
    function testPairs()
    {
        $expected   = array(
            1 => 'Adminer',
            4 => 'Dibi',
            2 => 'JUSH',
            3 => 'Nette'
        );
        $expectedId = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4
        );

        $this->assertEquals($expected, self::getDatabase()->application()->order("title")->fetchPairs("id", "title"));
        $this->assertEquals($expectedId, self::getDatabase()->application()->order("id")->fetchPairs("id", "id"));
    }

    /**
     * 14-where.phpt
     *
     * @test
     */
    public function testWhere()
    {
        $expected = array(
            array(4),
            array(1, 2, 3),
            array(1, 2, 3),
            array(1, 2),
            array(),
            array(1, 2, 3, 4),
            array(1, 3),
            array(3),
        );
        $software = self::getDatabase();
        foreach (array(
                     $software->application("id", 4),
                     $software->application("id < ?", 4),
                     $software->application("id < ?", array(4)),
                     $software->application("id", array(1, 2)),
                     $software->application("id", null),
                     $software->application("id", $software->application()),
                     $software->application("id < ?", 4)->where("maintainer_id IS NOT NULL"),
                     $software->application(array("id < ?" => 4, "author_id" => 12)),
                 ) as $result) {
            $this->assertEquals(
                array_shift($expected),
                array_keys(iterator_to_array($result->order("id")))
            // aggregation("GROUP_CONCAT(id)") is not available in all drivers
            );
        }
    }

    /**
     * IN operator
     * ----------
     * 26-in.phpt
     *
     * @test
     */
    public function testIn()
    {
        $expected = array(0, 1, 2, 3);
        $this->assertEquals(
            array_shift($expected),
            self::getDatabase()->application("maintainer_id", array())->count("*")
        );
        $this->assertEquals(
            array_shift($expected),
            self::getDatabase()->application("maintainer_id", array(11))->count("*")
        );
        $this->assertEquals(
            array_shift($expected),
            self::getDatabase()->application("NOT maintainer_id", array(11))->count("*")
        );
        $this->assertEquals(
            array_shift($expected),
            self::getDatabase()->application("NOT maintainer_id", array())->count("*")
        );
    }
}