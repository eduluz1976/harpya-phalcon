<?php

namespace harpya\phalcon;

class Config
{
    protected $values = [];

    /**
     * @param string $filename
     */
    public function loadFromFile($filename)
    {
        if (file_exists($filename) && is_readable($filename)) {
            $txt = file_get_contents($filename);
            $json = json_decode($txt, true);
            if (is_array($json)) {
                $this->values = $json;
            }
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if (is_scalar($key) && isset($this->values[$key])) {
            return $this->values[$key];
        } else {
            return $default;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        if (is_scalar($key)) {
            $this->values[$key] = $value;
        }
        return $this;
    }
}
