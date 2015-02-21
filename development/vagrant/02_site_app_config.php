<?php
return array(
	// 'language' => 'en',
	// 'language_fallback' => 'en',
	// 'locale' => 'en_US',
	'default_timezone' => 'Asia/Tokyo',
	'log_threshold' => Fuel::L_ALL,
	'always_load' => array(
		'packages' => array(
			'auth',
			'orm',
			'commentbox',
		),
	),
);
