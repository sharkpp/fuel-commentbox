<?php

Autoloader::add_core_namespace('Commentbox');

Autoloader::add_classes(array(
	'Commentbox\\Commentbox'          => __DIR__ . '/classes/commentbox.php',
	'Commentbox\\CommentboxException' => __DIR__ . '/classes/commentbox.php',
	'Commentbox\\Model_Commentbox'    => __DIR__ . '/classes/model/commentbox.php',

	// helper class
	'Commentbox\\Avatar'              => __DIR__ . '/classes/util/avatar.php',

));
