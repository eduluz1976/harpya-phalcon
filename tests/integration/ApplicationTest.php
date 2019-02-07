<?php

namespace harpya\phalcon\integration;

use \harpya\phalcon\Application;
use harpya\phalcon\Config;

class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadConfigFile()
    {
        $cfg = new Config();

        $cfg->loadFromFile(__DIR__ . '/assets/config.json');

        $this->assertEquals('value', $cfg->get('key'));
    }

    public function testLoadInvalidConfigFile()
    {
        $cfg = new Config();

        $cfg->loadFromFile(__DIR__ . '/assets/invalid-config.json');

        $this->assertEquals(false, $cfg->get('key'));
    }

    public function testAppLoadConfigFile()
    {
        $app = Application::getInstance(['config' => __DIR__ . '/assets/config.json']);

        $this->assertEquals('value', $app->getConfig()->get('key'));
    }
}
