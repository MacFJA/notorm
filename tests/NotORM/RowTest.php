<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 07/02/14
 * Time: 21:49
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

class RowTest extends NotORMBaseTestCase
{
    /**
     * Update row through property
     * ----------
     * 29-row-set.phpt
     *
     * @test
     */
    public function testRowSet()
    {
        $software = self::getDatabase();

        $application         = $software->application[1];
        $application->author = $software->author[12];
        $this->assertEquals(1, $application->update() . '');
        $application->update(array("author_id" => 11));
    }

    /**
     * Custom row class
     * ----------
     * 30-rowclass.phpt
     *
     * @test
     */
    public function testRowClass()
    {
        $expected = array('Adminer', 'Jakub Vrana');

        self::getDatabase()->rowClass = 'NotORM\\TestRow';

        $application = self::getDatabase()->application[1];
        $this->assertEquals(array_shift($expected), $application['test_title']);
        $this->assertEquals(array_shift($expected), $application->author['test_name']);

        self::getDatabase()->rowClass = 'NotORM\\Row';
    }
}

class TestRow extends Row
{

    function offsetExists($key)
    {
        return parent::offsetExists(preg_replace('~^test_~', '', $key));
    }

    function offsetGet($key)
    {
        return parent::offsetGet(preg_replace('~^test_~', '', $key));
    }

}