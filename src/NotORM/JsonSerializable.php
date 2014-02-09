<?php
/**
 * Created by JetBrains PhpStorm.
 * User: florent
 * Date: 08/02/14
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */

namespace NotORM;

if (interface_exists('\JsonSerializable', false)) {
    interface JsonSerializable extends \JsonSerializable
    {

    }
} else {
    interface JsonSerializable
    {
        /**
         * Specify data which should be serialized to JSON
         *
         * Serializes the object to a value that can be serialized natively by json_encode().
         *
         * @return mixed
         */
        public function jsonSerialize();
    }
}