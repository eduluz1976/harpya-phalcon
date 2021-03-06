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

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }
        return $this->request;
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

        return $this->delete(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
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

        return $this->post(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
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

        return $this->put(
            $route,
            function (...$values) use ($app, $sTarget,$vars) {
                $app->execRequest($app, $sTarget, $vars, $values);
            }
        );
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
