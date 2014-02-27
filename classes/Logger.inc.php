<?php
/**
 *	Abstract superclass for logging messages.
 */
abstract class Logger {

	/**
	 *	Logs the given message.
	 */ 
	public abstract function log($m);

	/**
 	 *	Logs the given error message.
	 */
	public abstract function error($e);

	/**
	 *	Logs the given warning message.
	 */
	public abstract function warning($w);

	/**
	 *	Logs the given message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 *	Note: 'e' from 'extended', PHP doesn't allow method overloading...
	 */
	public function eLog($location, $m);

	/**
	 *	Logs the given error message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eError($location, $e);

	/**
	 *	Logs the given warning message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eWarning($location, $w);
	
}
