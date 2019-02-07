<?php

namespace harpya\phalcon;

trait Whitelist
{
    protected $whitelist = [];

    /**
     * @param $name
     */
    public function addWhitelistedRoute($name)
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $this->whitelist[$item] = 1;
            }
        } elseif (is_scalar($name)) {
            $this->whitelist[$name] = 1;
        }
    }

    /**
     * Method that checks if the route name is whitelisted or not.
     *
     * @param string $name
     * @return bool
     */
    public function isWhitelisted($name)
    {
        if (isset($this->whitelist[$name])) {
            return true;
        }
        return false;
    }
}
