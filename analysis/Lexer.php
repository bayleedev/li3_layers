<?php

namespace li3_blocks\analysis;

/**
 * Compiles a file into a PHP Class
 */
class Lexer {

	/**
	 * The current `File` object.
	 *
	 * @var object
	 */
	public $_file = null;

	/**
	 * The template files that can be overwritten.
	 *
	 * @var array
	 */
	protected $_templateFiles = array(
		'class' => '/resources/templates/class.tpl',
		'method' => '/resources/templates/method.tpl',
	);

	/**
	 * Contents of the template files.
	 *
	 * @var array
	 */
	protected $_templates = array(
		'class' => null,
		'method' => null,
	);

	/**
	 * Sets up the two template files, and calls `lex` on our target file.
	 */
	public function __construct() {
		foreach ($this->_templates as $key => &$value) {
			$value = file_get_contents(HIERARCHY_ROOT_PATH . $this->_templateFiles[$key]);
		}
	}

	/**
	 * Creates a new `File` object and compiles it to php classes.
	 *
	 * @param  object $file Full path of the file.
	 * @return void
	 */
	public function lex($file) {
		$this->_file = $file;
		return $this->compileClass();
	}

	/**
	 * Will compile the methods for the current file.
	 *
	 * @return string
	 */
	public function compileMethods() {
		$methods = array();
		$from = array('{:name:}', '{:contents:}');
		foreach ($this->_file->blocks() as $name => $value) {
			$methods[] = str_replace($from, compact('name', 'value'), $this->_templates['method']);
		}
		return implode($methods, PHP_EOL);
	}

	/**
	 * Compiles the entire class into a string. Like a boss.
	 *
	 * @return string
	 */
	public function compileClass() {
		$parent = $this->_file->parent();
		$values = array(
			'{:name:}' => $this->_file->name(),
			'{:parent:}' => $parent ? $parent->name_space() . '/' . $parent->name() : 'foo',
			'{:contents:}' => $this->compileMethods(),
			'{:namespace:}' => $this->_file->name_space(),
		);
		return str_replace(array_keys($values), $values, $this->_templates['class']);
	}

}