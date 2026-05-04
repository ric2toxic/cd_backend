<?php

// store email ids
$store_email_ids = array(
	'JP' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Jaipur Store',
		),

	),
	'ST' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Surat Store',
		),

	),
	'DL' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Delhi Store',
		),

	),
	'MU' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Mumbai Store',
		),

	),

	'KL' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Kolkata Store',
		),

	),

	'BL' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Bangalore Store',
		),

	),
);

define('STORE_EMAIL_IDS', $store_email_ids);

// Email IDs
$email_ids = array(
	'madhur' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'Madhur Bhaiya',
	),
	'rakesh' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'Rakesh Shekhawat',
	),
	'chandan' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'Chandan Agarwal',
	),
	'rohit' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'Rohit Dangayach',
	),
	'operations' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'WholesaleBox Operations',
	),
	'nitesh' => array(
		'email_id' => 'ric2toxicshop@gmail.com',
		'name' => 'WholesaleBox Nitesh Bhutoria',
	),
	'BD' => array(
		'DL' => array(array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Ankur Trivedi',
		),
			array(
				'email_id' => 'ric2toxicshop@gmail.com',
				'name' => 'WholesaleBox Delhi Seller Onboarding',
			),
		),
		'AG_DL' => array(array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Agra Seller Onboarding',
		),
		),
		'JP' => array(array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Jaipur Seller Onboarding',
		),
		),
		'ST' => array(array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Surat Seller Onboarding',
		),
		),
	),
);
define('EMAIL_IDS', $email_ids);

// Pickup email ids
$pickup_email_ids = array(
	'JP' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Jaipur Pickup',
		),

	),
	'ST' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Surat Pickup',
		),
	),

	'DL' => array(
		array(
			'email_id' => 'ric2toxicshop@gmail.com',
			'name' => 'WholesaleBox Delhi Operations',
		),
	),

);

define('PICKUP_EMAIL_IDS', serialize($pickup_email_ids));
