<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 04/02/14
 * Time: 23:13
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

class MultiResultTest extends NotORMBaseTestCase
{
    /**
     * 12-via.phpt
     *
     * @test
     */
    public function testVia()
    {
        $expected = array(
            array('author' => 'Jakub Vrana', 'title' => 'Adminer'),
            array('author' => 'David Grudl', 'title' => 'Nette'),
            array('author' => 'David Grudl', 'title' => 'Dibi'),
        );

        foreach (self::getDatabase()->author() as $author) {
            foreach ($author->application()->via("maintainer_id") as $application) {
                $line = array_shift($expected);
                $this->assertEquals($line['author'], $author['name']);
                $this->assertEquals($line['title'], $application['title']);
            }
        }
    }

    /**
     * IN operator with MultiResult
     * ----------
     * 27-in-multi.phpt
     *
     * @test
     */
    public function testIn()
    {
        $expected = array(
            array('author' => 11, 'application_id' => 1, 'tag_id' => 21),
            array('author' => 11, 'application_id' => 1, 'tag_id' => 22),
            array('author' => 11, 'application_id' => 2, 'tag_id' => 23),
            array('author' => 12, 'application_id' => 3, 'tag_id' => 21),
            array('author' => 12, 'application_id' => 4, 'tag_id' => 21),
            array('author' => 12, 'application_id' => 4, 'tag_id' => 22),
        );

        $software = self::getDatabase();
        foreach ($software->author()->order("id") as $author) {
            foreach ($software->application_tag("application_id", $author->application())->order(
                         "application_id, tag_id"
                     ) as $application_tag) {
                $line = array_shift($expected);
                $this->assertEquals($line['author'], $author . '');
                $this->assertEquals($line['application_id'], $application_tag['application_id']);
                $this->assertEquals($line['tag_id'], $application_tag['tag_id']);
            }
        }
    }

    /**
     * Using the same MultiResult several times
     * ----------
     * 35-multiresult-loop.phpt
     *
     * @test
     */
    public function testMultiResultLoop()
    {
        $expected    = array(2, 2, 2, 2);
        $application = self::getDatabase()->application[1];
        for ($i = 0; $i < 4; $i++) {
            $this->assertEquals(array_shift($expected), count($application->application_tag()));
        }
    }
}
