<?php

namespace harpya\phalcon\interfaces;


interface ReportHandler {

    public function logException($ex, $additionalData=[]);

}
