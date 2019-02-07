<?php

namespace harpya\phalcon;

/**
 * Trait ConfigLoader
 * @package harpya\ca
 */
trait ConfigLoader
{
    protected $config;

    /**
     * @param $filename
     */
    protected function loadConfig($filename)
    {
        $this->getConfig()->loadFromFile($filename);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = new Config();
        }

        return $this->config;
    }
}
