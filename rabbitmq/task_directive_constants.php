<?php 
//GENERAL_QUEUE

define('TENTATIVEADVANCE',serialize(array('function_name' => 'curlRequestCRMTask')));
/*
* send notification to all customers of  seller which all purchase a product in specfic category
*/
define('SLRNEWSKUNOTIFITOCSTMR',serialize(array('function_name' => 'sendSellerNewSkuUploadNotificationToCustomer')));
/*
* send push notification to seller's custommers from specfic store 
* if any product quantity transfer to that particular store
*/
define('NOTIFICATIONTOSTORECUSTOMERS',serialize(array('function_name' => 'sendNotificationToStoreCustomers')));

define('CUSTOMDEBITNOTE',serialize(array('function_name' => 'sendMailForCustomDn')));

//Generate purchase debit note email alert
define('WSBPURCHASEDEBITNOTE',serialize(array('function_name' => 'sendMailWsbPurchaseDn')));

//Canceled purchase debit note email alert
define('WSBPURCHASECANCELEDDEBITNOTE',serialize(array('function_name' => 'sendMailWsbPurchaseCancelDn')));

// bulk product csv upload from admin panel
define('BULK_PRODUCT_CSV_UPLOAD',serialize(array('function_name' => 'applyBulkProductCsvUpload')));

// bulk product csv upload from admin panel
define('WEBENGAGE_EVENT',serialize(array('function_name' => 'triggerWebengageEvent')));


// Bulk product csv data and show in product list page.
$product_list_data = array(
	'oc_product' 			=> 	array(
										'quantity' 	=> 'Set Quantity',
										'price' 	=> 'Transfer Price per piece',
										'commission'=> 'Commission',
										'mrp'		=> 'MRP',
										'weight'	=> 'Weight per piece',
										'hsn_code' 	=> 'HSN Code',  	
										'minimum'	=> 'Minimum Order Quantity',
										'status'	=> 'Product Status', 
										'store_sales' => 'Store Code', 
										'sort_order' => 'Sort Order', 
										'model' 	=> 'WSB Product Code',
										'sku'		=> 'Seller SKU',
										'cod_available' => 'Available on COD', 
										'non_returnable' => 'Non Returnable',
										'piece_in_set'	=> 'Piece in Set', 
										'only_for_search' => 'Keywords for Search only'
										
									 ),
	'oc_product_description'=> 	array(
										'name' 		=> 'Product Name',
										'set_description' => 'Set Description'
									 ),
);
define('PRODUCT_LIST_DATA',	serialize($product_list_data));

// Bulk product CSV data and using with rabbitMQ
$product_data = array(
	'oc_product' 			=> 	array(
										'quantity' 	=> 	array( 'validation_regex'=> '/^[0-9][0-9]*$/', 
													  		   'error_message' 	 => 'Set Quantity should be positive integer only.'
															),
										'price' 	=> 	array( 'validation_regex'=> '/^[0-9]{1,7}(?:\.[0-9]{0,4})?$/', 
													  		   'error_message' 	 => 'Enter valid positive price, greater than zero.'
															),
										'commission'=> 	array( 'validation_regex'=> '/^[0-9]{1,7}(?:\.[0-9]{0,4})?$/', 
													  		   'error_message' 	 => 'Enter valid positive commission, greater than zero.'
															),
										'mrp'		=> 	array( 'validation_regex'=> '/^[0-9]{1,7}(?:\.[0-9]{0,4})?$/', 
													  		   'error_message' 	 => 'Enter valid positive commission, greater than zero.'
															),
										'weight'	=> 	array( 'validation_regex'=> '/^[0-9]{1,7}(?:\.[0-9]{0,4})?$/', 
													  		   'error_message' 	 => 'Enter valid positive weight, greater than zero.'
															),
										'hsn_code' 	=> 	array( 'validation_regex'=> '/^([[:digit:]]{4}|[[:digit:]]{6}|[[:digit:]]{8})$/', 
													  		   'error_message' 	 => 'HSN code should be either 4-digit, 6-digit, or 8-digit.'
															),
										'minimum' 	=> 	array( 'validation_regex'=> '/^[1-9][0-9]*$/', 
													  		   'error_message' 	 => 'Minimum Order Quantity should be positive integer only, greater than zero.'
															),
										'status'	=> 	array( 'validation_regex'=> '/[0|1|2|3|4]$/', 
													  		   'error_message' 	 => 'Status should be either of 0/1/2/3/4.'
															),
										'store_sales'=>	array( 'validation_regex'=> '/\b(JP|ST|DL|MU|BL|KL|NO)\b/', 
													  		   'error_message' 	 => 'Store Code should be either of JP/ST/DL/BL/KL/MU/NO.'
															),
										'sort_order'=>	array( 'validation_regex'=> '/^[0-9][0-9]*$/', 
													  		   'error_message' 	 => 'Sort order should be positive integer only.'
															),
										'model' 	=> 	array( 'validation_regex'=> '/^([A-Z0-9_-]){8,64}$/', 
													  	  	   'error_message' 	 => 'WSB Product code should contain only alphabets(a-z,A-Z), digits(0-9), hyphen(-), or underscore( _ ).'
															),
										'cod_available'	=> 	array( 'validation_regex'=> '/[0|1]{1}$/', 
													  	  	   	   'error_message'   => 'Available on COD should contain only 0 or 1.'
																),
										'non_returnable'=> 	array( 'validation_regex'=> '/[0|1]{1}$/', 
													  	  		   'error_message'   => 'Non Returnable should contain only 0 or 1.'
																),
										'piece_in_set' 	=> 	array( 'validation_regex'=> '/^[1-9][0-9]*$/', 
													  		   		'error_message'  => 'Piece in Set should be positive integer only, greater than zero.'
															),
										'only_for_search' 	=> 	array( 'validation_regex'=> '/^[a-zA-Z0-9, \s]+$/', 
													  		   		'error_message'  => 'Please avoid special characters in Keywords for Search only.'
															),
									 ),
	'oc_product_description'=> 	array(
										'name' 		=> 	array( 'validation_regex'=> '/^[a-zA-Z0-9- \s]+$/', 
												 	  	   	   'error_message' 	=> 'Please avoid special characters other than space and hypen in product name.'
												 			 ),
										'set_description' => array( 'validation_regex'=> '/^[a-zA-Z0-9-;:,= \s]+$/', 
										                            'error_message'   => 'Please avoid special characters in set description.'
									 						),
									 ),
);
define('PRODUCT_DATA',	serialize($product_data));

// COD order alert on desktop dialer
define('ADD_LEAD_IN_DESKTOP_DIALER',serialize(array('function_name' => 'addAlertInDesktopDialer')));

//RBL Order Punch API request log
define('RBL_ORDER_PUNCH_REQUEST_LOG', serialize(array('function_name' => 'updateRBLLogForOrderPunchAPI')));


