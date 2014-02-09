<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 07/02/14
 * Time: 21:47
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

class LiteralTest extends NotORMBaseTestCase
{
    /**
     * Literal value with parameters
     * ----------
     * 28-literal.phpt
     *
     * @test
     */
    public function testLiteral()
    {
        foreach (self::getDatabase()->author()->select(new Literal("? + ?", 1, 2))->fetch() as $val) {
            $this->assertEquals(3, $val);
        }
    }

    /**
     * DateTime processing
     * ----------
     * 31-datetime.phpt
     *
     * @test
     */
    public function testDateTime()
    {
        $date     = new \DateTime("2011-08-30");
        $software = self::getDatabase();

        $software->application()->insert(
            array(
                "id"        => 5,
                "author_id" => 11,
                "title"     => $date,
                "slogan"    => new Literal("?", $date),
            )
        );

        $application = $software->application()->where("title = ?", $date)->fetch();
        $this->assertEquals('2011-08-30 00:00:00', $application['slogan']);
        $application->delete();
    }
}