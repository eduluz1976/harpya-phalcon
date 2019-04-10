<?php

namespace harpya\phalcon\middleware;

use harpya\phalcon\Application;
use \Phalcon\Events\Event;
use \Phalcon\Mvc\Micro;
use \Phalcon\Mvc\Micro\MiddlewareInterface;


/**
 * RequestMiddleware
 *
 * Check incoming payload
 */
class RequestMiddleware implements MiddlewareInterface
{
    /**
     * Before the route is executed
     *
     * @param Event $event
     * @param Micro $application
     * @return bool
     */
    public function beforeExecuteRoute(Event $event, Micro $application)
    {
        $body = Application::getInstance()->getRequest()->getRawBody();

        if (empty($body)) {
            return true;
        }

        $json = json_decode($body, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $application->response->redirect('/malformed');
            $application->response->send();

            return false;
        }

        if (!is_array($json)) {
            $json = [];
        }

        $postVars = (isset($_POST) && is_array($_POST))?$_POST:[];

        $_POST = array_replace($postVars, $json);
        Application::getInstance()->initRequest();

        return true;
    }

    /**
     * Calls the middleware
     *
     * @param Micro $application
     *
     * @return bool
     */
    public function call(Micro $application)
    {
        return true;
    }
}
