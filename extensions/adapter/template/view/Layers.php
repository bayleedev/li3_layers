<?php

namespace li3_layers\extensions\adapter\template\view;

use lithium\template\view\adapter\File;

class Layers extends File {

	protected $_classes = array(
		'compiler' => 'li3_layers\template\view\Compiler',
		'router' => 'lithium\net\http\Router',
		'media'  => 'lithium\net\http\Media'
	);

	public function render($template, $data = array(), array $options = array()) {
		print_r(compact('template'));
		return 'hi';
	}

}