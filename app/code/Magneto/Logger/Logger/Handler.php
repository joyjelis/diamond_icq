<?php
namespace Magneto\Logger\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger;

class Handler extends BaseHandler {
	/**
	 * Logging level
	 * @var int
	 */
	protected $loggerType = Logger::WARNING;

	/**
	 * File name
	 * @var string
	 */
	protected $fileName = '/var/log/magneto_logs.log';

	/**
	 * @inheritDoc
	 */
	public function write($record) {
		parent::write(['formatted' => $record]);
	}

	public function log($record) {
		parent::write(['formatted' => $record]);
	}
}