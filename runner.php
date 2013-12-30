<?php

namespace li3_layers;

define('LAYERS_ROOT_PATH', __DIR__);
define('APP_VIEW_PATH', __DIR__ . '/views');

require_once __DIR__ . '/models/File.php';
require_once __DIR__ . '/analysis/Lexer.php';
require_once __DIR__ . '/analysis/Parser.php';

new Runner();

// Determiens which aspects need to be ran lexer/parser with which files
class Runner {

	/**
	 * Class dependencies that can be overwritten.
	 *
	 * @var array
	 */
	protected $_classes = array(
		'file' => 'li3_layers\models\File',
		'lexer' => 'li3_layers\analysis\Lexer',
		'parser' => 'li3_layers\analysis\Parser',
	);

	public function __construct() {
		extract($this->_classes);
		$lexer = new $lexer();
		$file = new $file(__DIR__ . '/views/home/index.html.php');
		do {
			print_r(array(
				'file' => $file->filePath(),
				'foo' => $lexer->lex($file),
			));
		} while($file = $file->parent());
	}

}

?>