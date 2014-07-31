<?php

namespace OPI\DeviceModel;

require_once 'models/OPIBackend.php';

function shutdown( $action )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->shutdown( \OPI\session\gettoken(), $action);

	return $status;
}
