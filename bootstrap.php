<?php

Autoloader::add_core_namespace('Commentbox');

Autoloader::add_classes(array(
	'Commentbox\\Commentbox' => __DIR__ . '/classes/commentbox.php',
	'Commentbox\\CommentboxException' => __DIR__ . '/classes/commentbox.php',

));
