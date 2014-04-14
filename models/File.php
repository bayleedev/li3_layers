<?php

namespace li3_layers\models;

use lithium\core\Libraries;

class File {

	const T_BLOCK = 0;

	const T_PARENT = 1;

	public $file = null;

	public $contents = null;

	protected static $_terminals = array(
		self::T_BLOCK => "/{:block \"([^\"]+)\"}(.*){block:}/msU",
		self::T_PARENT => "/{:parent (?:layout )?\"([^\"]+)\":}/msU",
	);

	public function __construct($file) {
		$this->file = $file;
	}

	public function contents() {
		return $this->content ?: ($this->content = file_get_contents($this->file));
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
		preg_match_all(static::$_terminals[self::T_BLOCK], $this->contents(), $matches);
		foreach ($matches[1] as $key => $match) {
			$blocks[$match] = $matches[2][$key];
		}
		if ($main = $this->mainBlock()) {
			$blocks['main'] = $main;
		}
		return $this->replaceBlockCalls($blocks);
	}

	public function replaceBlockCalls(array $blocks) {
		foreach ($blocks as &$block) {
			$block = preg_replace(static::$_terminals[self::T_BLOCK], '<?=\$this->$1();?>', $block);
		}
		return $blocks;
	}

	public function mainBlock() {
		if ($this->parent() !== false) {
			return;
		}
		return $this->contents();
	}

	/**
	 * Determines the parent and returns it, `false` otherwise.
	 *
	 * @return mixed
	 */
	public function parent() {
		preg_match(static::$_terminals[self::T_PARENT], $this->contents(), $matches);
		return isset($matches[1]) ? new static(APP_VIEW_PATH . '/' . $matches[1] . '.html.php') : false;
	}

	/**
	 * Determines the name of the file.
	 *
	 * @return mixed
	 */
	public function name() {
		$info = pathinfo($this->file);
		preg_match('/([^.]+)/', $info['basename'], $matches);
		return $matches[1];
	}

	/**
	 * The namespace the file 'class' should be in.
	 *
	 * @todo Change to lowerCamelCase `namespace` is a T_NAMESPACE
	 * @return string
	 */
	public function name_space() {
		$info = pathinfo($this->file);
		$base = '\li3_layers\resources\tmp\cache\compiled\\';
		return $base . str_replace('/', '\\', substr($info['dirname'], strpos($info['dirname'], '/views/') + 1));
	}

	/**
	 * Gives the current relative file path.
	 *
	 * This is useful for the cache since the directories mirror each other.
	 *
	 * @return string
	 */
	public function filePath() {
		return substr($this->file, strpos($this->file, '/views/'));
	}

	public function lastModified() {
		return filemtime($this->file);
	}

	public function cacheFile() {
		$appPath = Libraries::get(true, 'path');
		$cachePath = Libraries::get(true, 'resources') . '/tmp/cache/classes';
		$relFile = substr($this->file, strlen($appPath) + strlen('/views'));
		return $cachePath . $relFile;
	}

	public function cacheExpired() {
		$cache = $this->cacheFile();
		$cacheCreated = file_exists($cache) ? filemtime($cache) : 0;
		return $this->lastModified() > $cacheCreated;
	}

}

?>