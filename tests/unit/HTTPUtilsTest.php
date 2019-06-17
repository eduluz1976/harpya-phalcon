<?php
namespace harpya\phalcon\unit;

use harpya\phalcon\HTTPUtils;
use \Phalcon\Http\Request;
use \Phalcon\Http\Response;
use \Phalcon\Events\Manager;


class HTTPUtilsTest extends \PHPUnit\Framework\TestCase {

    public function testSetGetResponse() {

        $obj = new class {
            use HTTPUtils;


        };

        $this->assertInstanceOf(Request::class, $obj->getRequest());
        $this->assertInstanceOf(Response::class, $obj->getResponse());

    }


}
