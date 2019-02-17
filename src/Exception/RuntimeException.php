<?php

namespace harpya\phalcon\Exception;

/**
 * Custom exception object, that allows set the
 * HTTP status code returned when triggered.
 *
 * Class RuntimeException
 * @package harpya\phalcon\Exception
 */
class RuntimeException extends \Exception
{
    protected $forcedHttpCode;

    /**
     * @return mixed
     */
    public function getForcedHttpCode()
    {
        return $this->forcedHttpCode;
    }

    /**
     * @param mixed $forcedHttpCode
     * @return RuntimeException
     */
    public function setForcedHttpCode($forcedHttpCode)
    {
        $this->forcedHttpCode = $forcedHttpCode;

        return $this;
    }
}
