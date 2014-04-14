<?php

namespace li3_layers\template\view;

use lithium\core\Libraries;
use li3_layers\action\UnwriteableException;

class Compiler extends \lithium\template\view\Compiler {

	protected static $_classes = array(
		'file' => 'li3_layers\models\File',
		'lexer' => 'li3_layers\analysis\Lexer',
		'parser' => 'li3_layers\analysis\Parser',
	);

	public static function compileFileToCache($file, array $options) {
		$lexerClass = static::$_classes['lexer'];
		$lexer = new $lexerClass();
		do {
			$cacheFile = $file->cacheFile();
			if ($file->cacheExpired()) {
				$wroteDir = mkdir(dirname($cacheFile), 0755, true);
				$wroteFile = file_put_contents($cacheFile, $lexer->lex($file));
				if ($wroteDir === false || $wroteFile === false) {
					throw new UnwriteableException("File not writeable `{$cacheFile}`");
				}
			}
		} while($file = $file->parent());
	}

	public static function template($file, array $options = array()) {
		$options += array(
			'cachePath' => Libraries::get(true, 'resources') . '/tmp/cache/templates',
			'fallback' => false,
		);
		$fileClass = static::$_classes['file'];
		$file = new $fileClass($file);
		// TODO readonly
		static::compileFileToCache($file, $options);
		return $file->file;
	}

}