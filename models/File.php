<?php

namespace li3_blocks\models;

// Helps break down a single file
class File {

	const T_BLOCK = 0;

	const T_PARENT = 1;

	protected $_file = null;

	protected $_contents = null;

	protected static $_terminals = array(
		self::T_BLOCK => "/{:block \"([^\"]+)\"}(.*){block:}/msU",
		self::T_PARENT => "/{:parent \"([^\"]+)\":}/msU",
	);

	public function __construct($file) {
		$this->_file = $file;
		$this->_contents = file_get_contents($file);
	}

	/**
	 * Determiens if this file is a template or a view.
	 *
	 * @return boolean
	 */
	public function isTemplate() {
		return $this->parent() !== false;
	}

	/**
	 * Determiens if this file is a template or a view.
	 *
	 * @return boolean
	 */
	public function isView() {
		return $this->parent() === false;
	}

	/**
	 * Returns all the blocks of the view/template.
	 *
	 * @return array
	 */
	public function blocks() {
		$blocks = $matches = array();
		preg_match_all(static::$_terminals[self::T_BLOCK], $this->_contents, $matches);
		foreach ($matches[1] as $key => $match) {
			$blocks[$match] = $matches[2][$key];
		}
		return $blocks;
	}

	/**
	 * Determines the parent and returns it, `false` otherwise.
	 *
	 * @return mixed
	 */
	public function parent() {
		$file = get_called_class();
		preg_match(static::$_terminals[self::T_PARENT], $this->_contents, $matches);
		return isset($matches[1]) ? new $file(APP_VIEW_PATH . '/' . $matches[1] . '.html.php') : false;
	}

	/**
	 * Determines the name of the file.
	 *
	 * @return mixed
	 */
	public function name() {
		$info = pathinfo($this->_file);
		preg_match('/([^.]+)/', $info['basename'], $matches);
		return $matches[1];
	}

	/**
	 * The namespace the file 'class' should be in.
	 *
	 * @todo  rename to something `lowerCamelCase`.
	 * @return string
	 */
	public function name_space() {
		$info = pathinfo($this->_file);
		$base = '/li3_hierarchy/resources/tmp/cache/compiled/';
		return $base . substr($info['dirname'], strpos($info['dirname'], '/views/') + 1);
	}

	/**
	 * Gives the current relative file path.
	 *
	 * This is useful for the cache since the directories mirror each other.
	 *
	 * @return string
	 */
	public function filePath() {
		return substr($this->_file, strpos($this->_file, '/views/'));
	}

}

?>