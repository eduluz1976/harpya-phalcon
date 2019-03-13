<?php
namespace harpya\phalcon\Report;


use harpya\phalcon\interfaces\ReportHandler;
use harpya\phalcon\Report;

class NullReport extends Report implements ReportHandler {

    public function logException($ex, $additionalData = [])
    {
        //
    }
}
