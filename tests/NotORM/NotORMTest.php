<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 03/02/14
 * Time: 20:56
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

class NotORMTest extends NotORMBaseTestCase
{
    /**
     * Basic operations - Debug
     * ----------
     * 1-basic.phpt / Edited
     *
     * @test
     */
    public function testListApplicationsDebug()
    {
        self::getDatabase()->debug = true;
        $expected                  = array(
            array('title' => 'Adminer', 'author' => 'Jakub Vrana', 'tags' => array('PHP', 'MySQL')),
            array('title' => 'JUSH', 'author' => 'Jakub Vrana', 'tags' => array('JavaScript')),
            array('title' => 'Nette', 'author' => 'David Grudl', 'tags' => array('PHP')),
            array('title' => 'Dibi', 'author' => 'David Grudl', 'tags' => array('PHP', 'MySQL')),
        );
        $software                  = self::getDatabase();
        foreach ($software->application() as $application) {
            $line = array_shift($expected);
            $this->assertEquals($line['title'], $application['title']);
            $this->assertEquals($line['author'], $application->author['name']);
            foreach ($application->application_tag() as $application_tag) {
                $this->assertEquals(array_shift($line['tags']), $application_tag->tag['name']);
            }
        }
        self::getDatabase()->debug = false;
    }

    /**
     * Basic operations
     * ----------
     * 1-basic.phpt
     *
     * @test
     */
    public function testListApplications()
    {
        $expected = array(
            array('title' => 'Adminer', 'author' => 'Jakub Vrana', 'tags' => array('PHP', 'MySQL')),
            array('title' => 'JUSH', 'author' => 'Jakub Vrana', 'tags' => array('JavaScript')),
            array('title' => 'Nette', 'author' => 'David Grudl', 'tags' => array('PHP')),
            array('title' => 'Dibi', 'author' => 'David Grudl', 'tags' => array('PHP', 'MySQL')),
        );
        $software = self::getDatabase();
        foreach ($software->application() as $application) {
            $line = array_shift($expected);
            $this->assertEquals($line['title'], $application['title']);
            $this->assertEquals($line['author'], $application->author['name']);
            foreach ($application->application_tag() as $application_tag) {
                $this->assertEquals(array_shift($line['tags']), $application_tag->tag['name']);
            }
        }
    }

    /**
     * Single row detail
     * ----------
     * 2-detail.phpt
     *
     * @test
     */
    public function testApplicationAttributes()
    {
        $expected    = array(
            array('key' => 'id', 'value' => 1),
            array('key' => 'author_id', 'value' => 11),
            array('key' => 'maintainer_id', 'value' => 11),
            array('key' => 'title', 'value' => 'Adminer'),
            array('key' => 'web', 'value' => 'http://www.adminer.org/'),
            array('key' => 'slogan', 'value' => 'Database management in single PHP file')
        );
        $software    = self::getDatabase();
        $application = $software->application[1];
        foreach ($application as $key => $val) {
            $line = array_shift($expected);
            $this->assertEquals($line['key'], $key);
            $this->assertEquals($line['value'], $val);
        }
    }

    /**
     * Search and order items
     * ----------
     * 3-search-order.phpt
     *
     * @test
     */
    public function testSearchOrder()
    {
        $expected = array(
            'Adminer',
            'Dibi',
            'JUSH'
        );

        foreach (self::getDatabase()->application("web LIKE ?", "http://%")->order("title")->limit(3) as $application) {
            $this->assertEquals(array_shift($expected), $application['title']);
        }
    }

    /**
     * Search and order items (Edited)
     * ----------
     * 3-search-order.phpt / Edited
     *
     * @test
     */
    public function testSearchOrderLimit()
    {
        $expected = array(
            'Adminer',
            'Dibi',
        );

        foreach (self::getDatabase()->application("web LIKE ?", "http://%")->order("title")->limit(2) as $application) {
            $this->assertEquals(array_shift($expected), $application['title']);
        }
    }

    /**
     * Find one item by title
     * ----------
     * 4-findone.phpt
     *
     * @test
     */
    public function testFetch()
    {
        $expected    = array('PHP', 'Database management in single PHP file');
        $application = self::getDatabase()->application("title", "Adminer")->fetch();
        foreach ($application->application_tag("tag_id", 21) as $application_tag) {
            $this->assertEquals(array_shift($expected), $application_tag->tag["name"]);
        }
        $this->assertEquals(
            array_shift($expected),
            self::getDatabase()->application("title", "Adminer")->fetch("slogan")
        );
    }

    /**
     * Calling __toString()
     * ----------
     * 5-tostring.phpt
     *
     * @test
     */
    public function testToString()
    {
        $expected = array(1, 2, 3, 4);
        foreach (self::getDatabase()->application() as $application) {
            $this->assertEquals(array_shift($expected), "$application");
        }
    }

    /**
     * 7-subquery.phpt
     *
     * @test
     */
    public function testSubQuery()
    {
        $expected    = array('Adminer', 'JUSH', 'Nette', 'Dibi');
        $unknownBorn = self::getDatabase()->author("born", null); // authors with unknown date of born
        foreach (self::getDatabase()->application("author_id", $unknownBorn) as $application) { // their applications
            $this->assertEquals(array_shift($expected), $application['title']);
        }
    }

    /**
     * 13-join.phpt
     *
     * @test
     */
    public function testJoin()
    {
        $expected = array(
            array('author' => 'David Grudl', 'title' => 'Dibi'),
            array('author' => 'David Grudl', 'title' => 'Nette'),
            array('author' => 'Jakub Vrana', 'title' => 'Adminer'),
            array('author' => 'Jakub Vrana', 'title' => 'JUSH')
        );

        $expectedTag = array('PHP', 'MySQL', 'JavaScript');

        foreach (self::getDatabase()->application()->order("author.name, title") as $application) {
            $line = array_shift($expected);
            $this->assertEquals($line['author'], $application->author['name']);
            $this->assertEquals($line['title'], $application['title']);
        }

        foreach (self::getDatabase()->application_tag("application.author.name", "Jakub Vrana")->group(
                     "application_tag.tag_id"
                 ) as $application_tag) {
            $this->assertEquals(array_shift($expectedTag), $application_tag->tag["name"]);
        }
    }

    /**
     * 15-multiple.phpt
     *
     * @test
     */
    public function testMultiple()
    {
        $expected = array(
            array('application' => 1, 'tag' => 22),
            array('application' => 1, 'tag' => 21)
        );

        $application = self::getDatabase()->application[1];
        foreach ($application->application_tag()->select("application_id", "tag_id")->order(
                     "application_id DESC",
                     "tag_id DESC"
                 ) as $application_tag) {
            $line = array_shift($expected);
            $this->assertEquals($line['application'], $application_tag['application_id']);
            $this->assertEquals($line['tag'], $application_tag['tag_id']);
        }
    }

    /**
     * Limit and offset
     * ----------
     * 16-offset.phpt
     *
     * @test
     */
    public function testOffsetLimit()
    {
        $expected    = array('MySQL', 'MySQL', 'MySQL');
        $application = self::getDatabase()->application[1];
        foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag) {
            $this->assertEquals(array_shift($expected), $application_tag->tag["name"]);
        }

        foreach (self::getDatabase()->application() as $application) {
            foreach ($application->application_tag()->order("tag_id")->limit(1, 1) as $application_tag) {
                $this->assertEquals(array_shift($expected), $application_tag->tag["name"]);
            }
        }
    }

    /**
     * Array offset
     * ----------
     * 19-array-offset.phpt
     *
     * @test
     */
    function testArrayOffset()
    {
        $expected = array(2, 2);
        $where    = array(
            "author_id"     => "11",
            "maintainer_id" => null,
        );
        $this->assertEquals(array_shift($expected), self::getDatabase()->application[$where]["id"]);

        $applications = self::getDatabase()->application()->order("id");
        $this->assertEquals(array_shift($expected), $applications[$where]["id"]);
    }

    /**
     * Simple UNION
     * ----------
     * 21-simple-union.phpt
     *
     * @test
     */
    function testSimpleUnion()
    {
        $expected     = array(23, 22, 21, 4, 3, 2, 1);
        $applications = self::getDatabase()->application()->select("id");
        $tags         = self::getDatabase()->tag()->select("id");
        foreach ($applications->union($tags)->order("id DESC") as $row) {
            $this->assertEquals(array_shift($expected), $row['id']);
        }
    }

    /**
     * INSERT or UPDATE
     * ----------
     * 22-insert-update.phpt
     *
     * @test
     */
    function testInsertUpdate()
    {
        $expected = array(1, 2, 1);
        for ($i = 0; $i < 2; $i++) {
            $this->assertEquals(
                array_shift($expected),
                self::getDatabase()->application()->insertUpdate(
                    array("id" => 5),
                    array("author_id" => 12, "title" => "Texy", "web" => "", "slogan" => $i)
                )
            );
        }
        $application = self::getDatabase()->application[5];
        $this->assertEquals(
            array_shift($expected),
            $application->application_tag()->insertUpdate(array("tag_id" => 21), array())
        );
        self::getDatabase()->application("id", 5)->delete();
    }

    /**
     * Select locking
     * ----------
     * 24-lock.phpt
     *
     * @test
     */
    public function testLock()
    {
        $this->assertEquals('SELECT * FROM application FOR UPDATE', self::getDatabase()->application()->lock() . '');

    }

    /**
     * Backwards join
     * ----------
     * 25-backjoin.phpt
     *
     * @test
     */
    function testBackJoin()
    {
        $expected = array(
            array('author' => 'Jakub Vrana', 'tag' => 3),
            array('author' => 'David Grudl', 'tag' => 2)
        );

        foreach (self::getDatabase()->author()->select(
                     "author.*, COUNT(DISTINCT application:application_tag:tag_id) AS tags"
                 )->group("author.id")->order("tags DESC") as $autor) {
            $line = array_shift($expected);
            $this->assertEquals($line['author'], $autor['name']);
            $this->assertEquals($line['tag'], $autor['tags']);
        }
    }

    /**
     * IN with NULL value
     * ----------
     * 32-in-null.phpt
     *
     * @test
     */
    public function testInNull()
    {
        $expected = array(1, 2);
        foreach (self::getDatabase()->application("maintainer_id", array(11, null)) as $application) {
            $this->assertEquals(array_shift($expected), $application['id']);
        }
    }

    /**
     * Update primary key of a row
     * ----------
     * 34-update-primary.phpt
     *
     * @test
     */
    public function testUpdatePrimary()
    {
        $expected    = array(24, 25, 1, 1);
        $application = self::getDatabase()->tag()->insert(array('id' => 24, 'name' => 'HTML'));
        $this->assertEquals(array_shift($expected), $application['id']);
        $application['id'] = 25;
        $this->assertEquals(array_shift($expected), $application['id']);
        $this->assertEquals(array_shift($expected), $application->update() . '');
        $this->assertEquals(array_shift($expected), $application->delete() . '');
    }

    /**
     * Transactions
     * ----------
     * 17-transaction.phpt
     *
     * @test
     */
    public function testTransaction()
    {
        $expected              = array('99', '');
        $software              = self::getDatabase();
        $software->transaction = "BEGIN";
        $software->tag()->insert(array("id" => 99, "name" => "Test"));
        $this->assertEquals(array_shift($expected), $software->tag[99] . '');
        $software->transaction = "ROLLBACK";
        $this->assertEquals(array_shift($expected), $software->tag[99] . '');
    }
}