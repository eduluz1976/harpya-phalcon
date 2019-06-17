<?php

namespace harpya\phalcon;

/**
 * Trait Env
 * @package harpya\phalcon
 */
trait Env
{
    /**
     * Replacement for getenv, that allows add a default value, if the informed
     * key is undefined.
     *
     * @param string $key
     * @param bool $default
     * @return mixed
     */
    public function env($key, $default = false)
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        return $default;
    }

    /**
     *
     * Set a value for $key on $_ENV superglobal
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setEnv($key, $value)
    {
        if (!is_array($_ENV)) {
            $_ENV = [];
        }
        $_ENV[$key] = $value;
        return $this;
    }
}
