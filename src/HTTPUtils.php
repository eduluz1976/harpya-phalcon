<?php

namespace harpya\phalcon;

use \Phalcon\Http\Request;
use \Phalcon\Http\Response;
use \Phalcon\Events\Manager;

/**
 * Trait HTTPUtils
 * @package harpya\phalcon
 */
trait HTTPUtils
{
    protected $request;
    protected $response;
    protected $json;

    /**
     * @return \harpya\phalcon\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->initRequest();
        }
        return $this->request;
    }

    /**
     * Initialize the request property, with a
     * brand new \harpya\phalcon\Request object
     */
    public function initRequest()
    {
        $this->request = new \harpya\phalcon\Request();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Response();
        }
        return $this->response;
    }

    /**
     * @param string $filename
     */
    protected function loadRoutes($filename)
    {
        $this->getRequest();
        $this->getResponse();

        $this->getRouter()->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

        if (file_exists($filename)) {
            $app = &$this;
            include_once $filename;
        }
    }

    /**
     * Check the request and decide which route will be invoked.
     */
    public function handleRoutes()
    {
        $this->handle();
    }

    /**
     * Add an alternative action, if there is nothing that matches
     * whith the loaded routes
     *
     * @param mixed $sTarget
     */
    public function addRouteNotFound($sTarget)
    {
        $app = &$this;

        $this->notFound(
            function () use ($app, $sTarget) {
                $app->execRequest($app, $sTarget);
            }
        );
    }

    /**
     * @param $route
     * @param $sTarget
     * @param array $vars
     * @return mixed
     */
    public function get($route, $sTarget, $vars = [])
    {
        return $this->addGet($route, $sTarget, $vars);
    }

    /**
     * @param $route
     * @param $sTarget
     * @param array $vars
     * @return mixed
     */
    public function post($route, $sTarget, $vars = [])
    {
        return $this->addPost($route, $sTarget, $vars);
    }

    /**
     * @param $route
     * @param $sTarget
     * @param array $vars
     * @return mixed
     */
    public function options($route, $sTarget, $vars = [])
    {
        return $this->addOption($route, $sTarget, $vars);
    }

    /**
     * @param $route
     * @param $sTarget
     * @param array $vars
     * @return mixed
     */
    public function put($route, $sTarget, $vars = [])
    {
        return $this->addPut($route, $sTarget, $vars);
    }

    /**
     * @param $route
     * @param $sTarget
     * @param array $vars
     * @return mixed
     */
    public function delete($route, $sTarget, $vars = [])
    {
        return $this->addDelete($route, $sTarget, $vars);
    }

    /**
     * @param string $route
     * @param string $sTarget
     * @param array $vars
     * @return mixed
     */
    public function addGet($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('GET');
    }

    /**
     * @param string $route
     * @param string $sTarget
     * @param array $vars
     * @return mixed
     */
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

    /**
     * @param string $route
     * @param string $sTarget
     * @param array $vars
     * @return mixed
     */
    public function addDelete($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('DELETE');
    }

    /**
     * @param string $route
     * @param string $sTarget
     * @param array $vars
     * @return mixed
     */
    public function addPost($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('POST');
    }

    /**
     * @param string $route
     * @param string $sTarget
     * @param array $vars
     * @return mixed
     */
    public function addPut($route, $sTarget, $vars = [])
    {
        $app = &$this;

        return $this->map(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        )->via('PUT');
    }

    /**
     * @param Application $app
     * @param string $sTarget
     * @param array $vars
     * @param array $values
     * @throws \eduluz1976\action\exception\FunctionNotFoundException
     * @throws \eduluz1976\action\exception\InvalidURIException
     */
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

    /**
     * @param Application $app
     */
    protected function checkRequest($app)
    {
        $raw = trim($app->getRequest()->getRawBody());

        $json = json_decode($raw, true);

        if (is_array($json)) {
            $this->json = $json;
        }
    }

    /**
     * @param mixed $key
     * @return array
     */
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

    /**
     * @param array $middlewareList
     * @throws \Exception
     */
    public function loadMiddleware($middlewareList = [])
    {
        $eventsManager = new Manager();

        foreach ($middlewareList as $step => $classes) {
            foreach ($classes as $className) {
                if (!class_exists($className)) {
                    throw new \Exception("Class $className does not exists", 1);
                    continue;
                }
                $obj = new $className;

                if ($this->checkIfImplements($className)) {
                    $obj->setApplication($this);
                }

                $eventsManager->attach('micro', $obj);

                switch ($step) {
                    case 'before':
                        $this->before(new $className);
                        break;
                }
            }
        }

        $this->setEventsManager($eventsManager);
    }

    /**
     * @param $className
     * @return bool
     */
    protected function checkIfImplements($className)
    {
        $l = class_implements($className);
        if (isset($l['harpya\phalcon\interfaces\AccessApplication'])) {
            return true;
        } else {
            return false;
        }
    }
}
