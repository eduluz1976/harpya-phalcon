<?php

namespace harpya\phalcon;

/**
 * Class Request
 * @package harpya\phalcon
 */
class Request extends \Phalcon\Http\Request
{
    protected $json;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->checkRequest();
    }

    /**
     * Verify if the request have a JSON payload in their body,
     * and if yes, set an internal property with this value.
     */
    public function checkRequest()
    {
        $raw = trim($this->getRawBody());

        $json = json_decode($raw, true);

        if (is_array($json)) {
            $this->json = $json;
        }
    }

    /**
     * @param bool $key
     * @return bool
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
}
