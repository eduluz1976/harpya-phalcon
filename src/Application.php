<?php

namespace harpya\phalcon;

use harpya\api_auth\Core;
use harpya\phalcon\Report\NullReport;
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

    protected $authManager;
    protected $logHandler;

    /**
     * @return harpya\phalcon\interfaces\ReportHandler
     */
    public function getLogHandler()
    {
        return $this->logHandler;
    }


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
        if (isset($props['config'])) {
            $this->loadConfig($props['config']);
        }

        if (isset($props['log']) && !empty($props['log'])) {
            $this->loadLogHandler($props['log']);
        } else {
            $this->loadLogHandler([ new NullReport() ]);
        }


        if (isset($props['di'])) {
            parent::__construct($props['di']);
        }


        if (isset($props['auth'])) {
            $this->loadAuth($props['auth']);
        }

        if (isset($props['middleware'])) {
            $this->loadMiddleware($props['middleware']);
        }


        if (isset($props['routes'])) {
            $this->loadRoutes($props['routes']);
        }
    }

    /**
     * @param array $props
     * @throws \Exception
     */
    protected function loadAuth($props = [])
    {
        $filename = getenv('HARPYA_AUTH_OUTPUT_MASTER_FILE');
        $additional = [Core::PROP_OUTPUT_FILENAME => $filename];

        $props = array_replace($additional, $props);

        $this->getAuthManager($props);
        $this->getAuthManager()->loadMasterKey();
    }

    /**
     * @param array $props
     * @return Core
     */
    public function getAuthManager($props = [])
    {
        if (!$this->authManager) {
            $this->authManager = new Core($props);
        }
        return $this->authManager;
    }

    /**
     * Execute the application routing logic. Handles the request and try to match
     * to any rule already existent.
     */
    public function exec()
    {
        $exception = false;
        try {
            $this->handleRoutes();
        } catch (\harpya\phalcon\Exception\RuntimeException $e) {
            $resp = ['msg' => $e->getMessage(), 'code' => $e->getCode()];

            if ($e->getForcedHttpCode()) {
                $exception = $e->getForcedHttpCode();
            } else {
                $exception = 400;
            }
        } catch (\Exception $e) {
            $exception = 400;
            $resp = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        } finally {
            if ($exception) {

                $this->getLogHandler()->logException($e);

                $this->getResponse()->setContent(json_encode($resp))
                    ->setHeader('Content-Type', 'text/json')
                    ->setStatusCode($exception, 'Error')
                    ->send();
            }
        }
    }


    /**
     * @param array $logHandler
     * @param int $index
     */
    protected function loadLogHandler($logHandler=[], $index=0) {

        if (isset($logHandler[$index])) {
            $this->logHandler = $logHandler[$index];
        } else {
            $this->logHandler = reset($logHandler);
        }


    }

}
