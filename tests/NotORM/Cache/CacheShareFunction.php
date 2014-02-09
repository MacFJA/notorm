<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 08/02/14
 * Time: 20:03
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM\Cache;

use NotORM\NotORM;
use NotORM\NotORMBaseTestCase;

class CacheShareFunction extends NotORMBaseTestCase
{

    protected function sharedCode(NotORM $instance)
    {
        $applications = $instance->application();
        $application  = $applications->fetch();
        $application["title"];
        $application->author["name"];
        $this->assertEquals('SELECT * FROM application', $applications . '');
        $applications->__destruct();

        $applications = $instance->application();
        $application  = $applications->fetch();
        $this->assertEquals(
            'SELECT id, title, author_id FROM application',
            $applications . ''
        ); // get only title and author_id
        $application["slogan"]; // script changed and now we want also slogan
        $this->assertEquals(
            'SELECT * FROM application',
            $applications . ''
        ); // all columns must have been retrieved to get slogan
        $applications->__destruct();

        $applications = $instance->application();
        $applications->fetch();
        $this->assertEquals(
            'SELECT id, title, author_id, slogan FROM application',
            $applications . ''
        ); // next time, get only title, author_id and slogan
    }
}