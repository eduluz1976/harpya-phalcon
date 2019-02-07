<?php

namespace harpya\phalcon\unit;

use \harpya\phalcon\Application;

class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetInstance()
    {
        $app = Application::getInstance();
        $this->assertInstanceOf(Application::class, $app);
    }

    public function testApplication()
    {
        $props = [
            'config' => 1,
            'middleware' => [2],
            'routes' => 3
        ];

        $app = new class($props) extends Application {
            public $values = [];

            public function loadConfig($value)
            {
                $this->values['config'] = $value;
            }

            public function loadMiddleware($value = [])
            {
                $this->values['middleware'] = $value;
            }

            public function loadRoutes($value)
            {
                $this->values['routes'] = $value;
            }
        };

        $this->assertEquals(1, $app->values['config']);
        $this->assertEquals([2], $app->values['middleware']);
        $this->assertEquals(3, $app->values['routes']);
    }

    public function testWhitelist()
    {
        $app = Application::getInstance();
        $app->addWhitelistedRoute('test');

        $isWhitelisted = $app->isWhitelisted('test');
        $isNotWhitelisted = $app->isWhitelisted('none');

        $this->assertTrue($isWhitelisted);
        $this->assertFalse($isNotWhitelisted);

        $app->addWhitelistedRoute(['one', 'two', 'three']);

        $this->assertTrue($app->isWhitelisted('two'));
    }

    public function testEnv()
    {
        $_ENV['unit_test_abc'] = 123;

        $app = Application::getInstance();

        $this->assertEquals(123, $app->env('unit_test_abc'));

        $_ENV = null;

        $app->setEnv('unit_test_abc', 456);
        $this->assertEquals(456, $app->env('unit_test_abc'));
        $this->assertEquals(789, $app->env('unit_test_xyz', 789));
    }
}
