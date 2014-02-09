<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 07/02/14
 * Time: 21:15
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM\Structure;

use NotORM\NotORMBaseTestCase;

class ConventionTest extends NotORMBaseTestCase
{


    /**
     * Table prefix
     * ----------
     * 23-prefix.phpt
     *
     * @test
     */
    public function testPrefix()
    {
        $prefix = self::newDatabase(new Convention('id', '%s_id', '%s', 'prefix_'));

        $applications = $prefix->application("author.name", "Jakub Vrana");
        $this->assertEquals(
            'SELECT prefix_application.* FROM prefix_application LEFT JOIN prefix_author AS author ON prefix_application.author_id = author.id WHERE (author.name = \'Jakub Vrana\')',
            $applications . ''
        );

        self::unloadDatabase();
    }

    /**
     * Structure for non-conventional column
     * ----------
     * 33-structure.phpt
     *
     * @test
     */
    public function testStructure()
    {
        $expected   = array('Jakub Vrana', 'Adminer');
        $convention = self::newDatabase(new SoftwareConvention);
        $maintainer = $convention->application[1]->maintainer;
        $this->assertEquals(array_shift($expected), $maintainer['name']);
        foreach ($maintainer->application()->via('maintainer_id') as $application) {
            $this->assertEquals(array_shift($expected), $application['title']);
        }

        self::unloadDatabase();
    }
}

class SoftwareConvention extends Convention
{
    function getReferencedTable($name, $table)
    {
        switch ($name) {
            case 'maintainer':
                return parent::getReferencedTable('author', $table);
        }
        return parent::getReferencedTable($name, $table);
    }
}