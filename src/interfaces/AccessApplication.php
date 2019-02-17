<?php

namespace harpya\phalcon\interfaces;

/**
 * Interface AccessApplication
 * @package harpya\phalcon\interfaces
 */
interface AccessApplication
{
    /**
     * @param $app
     * @return mixed
     */
    public function setApplication(&$app);

    /**
     * @return mixed
     */
    public function getApplication();
}
