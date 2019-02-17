<?php

namespace harpya\phalcon\middleware;

use harpya\discover\Constants;
use harpya\phalcon\interfaces\AccessApplication;
use ParagonIE\Halite\Symmetric\Crypto;
use \Phalcon\Events\Event;
use \Phalcon\Mvc\Micro;
use \Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * Class ApiAuthMiddleware
 * @package harpya\phalcon\middleware
 */
class ApiAuthMiddleware implements AccessApplication
{
    public function beforeExecuteRoute(Event $event, Micro $application)
    {
        $auth = $application->request->getHeader('X-AUTH-API');

        $current = \Phalcon\DI::getDefault()->get('router')->getMatchedRoute()->getName();

        if ($application->isWhitelisted($current)) {
            return true;
        }

        if (empty($auth) || !$auth) {
            throw new \Exception('Auth token X-AUTH-API not present on header', Constants::EXCEPTION_ERROR_AUTH_API);
        }

        if (!$this->getApplication()->getAuthManager()->isValid($auth)) {
            $this->triggerInvalidToken();
        }

        return true;
    }

    /**
     * Placeholder method, that should be overwrite on final application to run the
     * correct actions when this access is unauthorized.
     */
    public function triggerInvalidToken()
    {
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

    /**
     * @param $app
     */
    public function setApplication(&$app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->app;
    }
}
