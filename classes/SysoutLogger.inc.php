<?php
/**
 *	Class used for logging messages to standard out.
 *  Standard error is not used.
 */
class SysoutLogger {

	/**
	 *	Initialize this new sysout logger.
	 */
	public function __construct() {
    // nothing to do
	}

	/**
	 *	Returns the current date and time as a string.
	 */
	private function getNow() {
		return date('D M d Y H:i:s');
	}

  /**
   *  Prints the given message to standard out.
   */
  private function out($msg) {
    echo($msg);
  }

	/**
	 *	Logs the given message.
	 */ 
	public function log($m) {
		$this->out('[LOG] [' . $this->getNow() . '] ' . $m . "\n");
	}

	/**
 	 *	Logs the given error message.
	 */
	public function error($e) {
		$this->out('[ERROR] [' . $this->getNow() . '] ' . $e . "\n");
	}

	/**
	 *	Logs the given warning message.
	 */
	public function warning($w) {
		$this->out('[WARNING] [' . $this->getNow() . '] ' . $w . "\n");
	}

	/**
	 *	Logs the given message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 *	Note: 'e' from 'extended', PHP doesn't know method overloading...
	 */
	public function eLog($location, $m) {		
		$this->out('[LOG] [' . $this->getNow() . "] [$location] " . $m . "\n");
	}

	/**
	 *	Logs the given error message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eError($location, $e) {
		$this->out('[ERROR] [' . $this->getNow() . "] [$location] " . $e . "\n");	
	}

	/**
	 *	Logs the given warning message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eWarning($location, $w) {
		$this->out('[WARNING] [' . $this->getNow() . "] [$location] " . $w . "\n");		
	}
	
}
