<?php
namespace harpya\phalcon\Report;


use harpya\phalcon\interfaces\ReportHandler;
use harpya\phalcon\Report;

class SentryReport extends Report implements ReportHandler {

    protected $client;
    protected $errorHandler;

    /**
     * @return \Raven_Client
     */
    public function getClient(): \Raven_Client
    {
        return $this->client;
    }

    /**
     * @param \Raven_Client $client
     * @return SentryReport
     */
    public function setClient(\Raven_Client $client): SentryReport
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return \Raven_ErrorHandler
     */
    public function getErrorHandler(): \Raven_ErrorHandler
    {
        return $this->errorHandler;
    }

    /**
     * @param \Raven_ErrorHandler $errorHandler
     * @return SentryReport
     */
    public function setErrorHandler(\Raven_ErrorHandler $errorHandler): SentryReport
    {
        $this->errorHandler = $errorHandler;
        return $this;
    }



    public function __construct($dsn, $options=[])
    {
        $this->setClient(new \Raven_Client($dsn,$options));
        $this->setErrorHandler(new \Raven_ErrorHandler($this->getClient()));
        $this->getErrorHandler()->registerExceptionHandler();
        $this->getErrorHandler()->registerErrorHandler();
        $this->getErrorHandler()->registerShutdownFunction();
    }





    public function logException($ex, $additionalData = [])
    {
        $this->getClient()->captureException($ex, $additionalData);
    }
}
