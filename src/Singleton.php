<?php

namespace harpya\phalcon;

/**
 * Trait Singleton
 * @package harpya\phalcon
 */
trait Singleton
{
    protected static $instance;

    /**
     * @param array $props
     * @return Application
     */
    public static function getInstance($props = [])
    {
        if (!self::$instance) {
            self::$instance = new Application($props);
        }

        return self::$instance;
    }
}
