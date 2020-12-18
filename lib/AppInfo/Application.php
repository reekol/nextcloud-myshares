<?php

namespace OCA\MyShares\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'myshares';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
