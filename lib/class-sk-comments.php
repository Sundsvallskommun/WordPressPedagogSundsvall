<?php
/**
 * Override for Disable comments support.
 *
 * Completeley disable Wordpress built-in comment support.
 *
 * @since 1.0.0
 *
 * @package sk-theme
 */
class SK_Comments {
	public function __construct() {
		return false;
	}
}