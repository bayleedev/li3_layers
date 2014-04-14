<?php

namespace li3_layers\actions;

use RuntimeException;

class UnwriteableException extends RuntimeException {

	protected $code = 500;

}