<?php

namespace harpya\phalcon;

use \Phalcon\Http\Request;
use \Phalcon\Http\Response;
use \Phalcon\Events\Manager;

trait HTTPUtils
{
    protected $request;
    protected $response;
    protected $json;

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function loadRoutes($filename)
    {
        $this->request = new Request();
        $this->response = new Response();

        $this->getRouter()->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

        if (file_exists($filename)) {
            $app = &$this;
            include_once $filename;
        }
    }

    public function handleRoutes()
    {
        $this->handle();
    }

    public function addRouteNotFound($sTarget)
    {
        $app = &$this;

        $this->notFound(
            function () use ($app, $sTarget) {
                $app->execRequest($app, $sTarget);
            }
        );
    }

    public function addGet($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('GET');

        return  $this->get(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
    }

    public function addOption($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('OPTIONS');
    }

    public function addDelete($route, $sTarget, $vars = [])
    {
        $app = &$this;

        $this->delete(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
    }

    public function addPost($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->post(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
    }

    public function addPut($route, $sTarget, $vars = [])
    {
        $app = &$this;

        $this->put(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
    }

    protected function execRequest($app, $sTarget, $vars = [], $values = [])
    {
        $target = \eduluz1976\action\Action::factory($sTarget);

        $response = $app->getResponse();
        $response->setHeader('Content-Type', 'text/json');

        if (is_array($vars) && is_array($values) && count($vars) == count($values)) {
            $map = array_combine($vars, $values);
            $target->exec([$app->getRequest(), &$response, $map]);
        } else {
            $target->exec([$app->getRequest(), &$response, $values]);
        }

        $app->getResponse()->send();
    }

    protected function checkRequest($app)
    {
        $raw = trim($app->getRequest()->getRawBody());

        $json = json_decode($raw, true);

        if (is_array($json)) {
            $this->json = $json;
        }
    }

    public function getJSON($key = false)
    {
        if (!$key) {
            return $this->json;
        } elseif (isset($this->json[$key])) {
            return $this->json[$key];
        } else {
            return false;
        }
    }

    public function loadMiddleware($middlewareList = [])
    {
        $eventsManager = new Manager();

        foreach ($middlewareList as $step => $classes) {
            foreach ($classes as $className) {
                if (!class_exists($className)) {
                    throw new \Exception("Class $className does not exists", 1);
                    continue;
                }
                $eventsManager->attach('micro', new $className);

                switch ($step) {
                    case 'before':
                        $this->before(new $className);
                        break;
                }
            }
        }

        $this->setEventsManager($eventsManager);
    }
}
