<?php

require_once __DIR__ . '/../../config.php';
require_once( DIR_SYSTEM . 'library/db/db.php' );
require_once(DIR_SYSTEM . '../vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/customer_activity_log.php');

$customer_activity_log_obj =  new CustomerActivityLog();
$customer_activity_log_obj->receiveCustomerActivityLogsQueue();
exit();

