<?php

namespace harpya\phalcon\unit;

use \harpya\phalcon\Application;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testSetGetConfig()
    {
        $cfg = new \harpya\phalcon\Config();
        $cfg->set('key', 'value');

        $this->assertEquals('value', $cfg->get('key'));
        $this->assertEquals('default', $cfg->get('key2', 'default'));
    }
}
