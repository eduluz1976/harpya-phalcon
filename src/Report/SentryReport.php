<?php
namespace harpya\phalcon\Report;


use harpya\phalcon\interfaces\ReportHandler;
use harpya\phalcon\Report;

/**
 *
 * Class SentryReport
 * @package harpya\phalcon\Report
 */
class SentryReport extends Report implements ReportHandler {

    /**
     * SentryReport constructor.
     * @param $dsn
     * @param array $options
     */
    public function __construct($dsn, $options=[])
    {
        $options['dsn'] = $dsn;
        \Sentry\init($options);
    }

    /**
     * @param $ex
     * @param array $additionalData
     */
    public function logException($ex, $additionalData = [])
    {
        \Sentry\captureException($ex);
    }
}
