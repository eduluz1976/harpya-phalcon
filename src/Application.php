<?php

namespace harpya\phalcon;

use Phalcon\Http\Request;
use Phalcon\Http\Response;

/**
 * Class Application
 * @package harpya\ca
 */
class Application extends \Phalcon\Mvc\Micro
{
    use HTTPUtils;
    use ConfigLoader;
    use Whitelist;
    use Singleton;
    use Env;

    /**
     * Application constructor.
     * @param $props
     */
    public function __construct($props)
    {
        if (is_array($props)) {
            $this->setProps($props);
        }
    }

    /**
     * @param array $props
     */
    protected function setProps($props = [])
    {
        if (isset($props['di'])) {
            parent::__construct($props['di']);
        }

        if (isset($props['config'])) {
            $this->loadConfig($props['config']);
        }

        if (isset($props['middleware'])) {
            $this->loadMiddleware($props['middleware']);
        }

        if (isset($props['routes'])) {
            $this->loadRoutes($props['routes']);
        }
    }

    /**
     * Execute the application routing logic. Handles the request and try to match
     * to any rule already existent.
     */
    public function exec()
    {
        try {
            $this->handleRoutes();
        } catch (\Exception $e) {
            $resp = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
            $this->getResponse()->setContent(json_encode($resp))
                ->setHeader('Content-Type', 'text/json')
                ->setStatusCode(400, 'OK')
                ->send();
        }
    }
}
