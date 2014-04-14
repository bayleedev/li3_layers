<?php

use lithium\core\Libraries;
use lithium\core\ConfigException;
use lithium\template\View;
use lithium\net\http\Media;

// Define path to plugin and other constants
defined('LAYERS_ROOT_PATH') OR define('LAYERS_ROOT_PATH', dirname(__DIR__));
defined('APP_VIEW_PATH') OR define('APP_VIEW_PATH', Libraries::get(true, 'path') . '/views');

/**
* Map to the new renderer
*/
Media::type('default', null, array(
	'view' => 'lithium\template\View',
	'renderer' => 'Layers',
	'loader' => 'Layers',
	'cast' => false,
	'paths' => array(
		'template' => array(
			LITHIUM_APP_PATH . '/views/{:controller}/{:template}.{:type}.php',
			'{:library}/views/{:controller}/{:template}.{:type}.php',
		),
		'layout' => array(
			LITHIUM_APP_PATH . '/views/{:controller}/{:layout}.{:type}.php',
			'{:library}/views/{:controller}/{:layout}.{:type}.php',
		)
	)
));

Media::applyFilter('view', function($self, $params, $chain) {
	$renderer = $params['handler']['renderer'];
	if (strpos($renderer, 'li3_layers') !== false) {
		$params['handler']['processes'] = array(
			'all' => array('template'),
			'template' => array('template'),
			'element' => array('element')
		);
	}
	return $chain->next($self, $params, $chain);
});