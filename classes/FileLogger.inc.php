<?php
/**
 *	Class used for logging messages to a file.
 *	A normal log is written to the log file. An error log is written
 *	to the log file and the error file. A warning is written to the 
 *	log file and the warning file.
 */
class FileLogger {

	private $logFile;
	private $errorFile;
	private $warningFile;

	/**
	 *	Initialize this new file logger with given file paths.
	 *	If the given file does not exist, it is created.
	 */
	public function __construct($logFile, $errorFile = '', $warningFile = '') {
		$this->logFile = fopen($logFile,'a+');
		if($this->logFile === FALSE) {
			echo("FileLogger::__construct() Could not open log file ($logFile).\n");		
			exit();
		}

		if($errorFile != '') {
			$this->errorFile = fopen($errorFile,'a+');
			if($this->errorFile === FALSE) {
				echo("FileLogger::__construct() Could not open error file ($errorFile).\n");		
				exit();
			}
		}

		if($warningFile != '') {
			$this->warningFile = fopen($warningFile,'a+');
			if($this->warningFile === FALSE) {
				echo("FileLogger::__construct() Could not open warning file ($warningFile).\n");		
				exit();
			}
		}
	}

	/**
	 *	Free held resources.
	 */
	public function __destruct() {
		if (is_resource($this->logFile)) {
			fclose($this->logFile);
		}
		if (is_resource($this->errorFile)) {
			fclose($this->errorFile);
		}
		if (is_resource($this->warningFile)) {
			fclose($this->warningFile);
		}
	}

	/**
	 *	Returns the current date and time as a string.
	 */
	private function getNow() {
		return date('D M d Y H:i:s');
	}

	/**
	 * Writes the given message to the log file if it exists.
	 */
	private function writeToLogFile($m) {
		if (fwrite($this->logFile, $m) === FALSE) {
			echo("FileLogger::writeToLogFile() Cannot write to log file.");
		}		
	}

	/**
	 * Writes the given message to the warning file if it exists.
	 */
	private function writeToWarningFile($m) {
		if (is_resource($this->warningFile) && fwrite($this->warningFile, $m) === FALSE) {
			echo("FileLogger::writeToWarningFile() Cannot write to warning file.");
		}		
	}

	/**
	 * Writes the given message to the error file if it exists.
	 */
	private function writeToErrorFile($m) {
		if (is_resource($this->errorFile) && fwrite($this->errorFile, $m) === FALSE) {
			echo("FileLogger::writeToErrorFile() Cannot write to error file.");
		}		
	}

	/**
	 *	Logs the given message.
	 */ 
	public function log($m) {
		$this->writeToLogFile('[LOG] [' . $this->getNow() . '] ' . $m . "\n");
	}

	/**
 	 *	Logs the given error message.
	 */
	public function error($e) {
		$this->writeToLogFile('[ERROR] [' . $this->getNow() . '] ' . $e . "\n");
		$this->writeToErrorFile('[' . $this->getNow() . '] ' . $e . "\n");
	}

	/**
	 *	Logs the given warning message.
	 */
	public function warning($w) {
		$this->writeToLogFile('[WARNING] [' . $this->getNow() . '] ' . $w . "\n");
		$this->writeToWarningFile('[' . $this->getNow() . '] ' . $w . "\n");
	}

	/**
	 *	Logs the given message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 *	Note: 'e' from 'extended', PHP doesn't know method overloading...
	 */
	public function eLog($location, $m) {		
		$this->writeToLogFile('[LOG] [' . $this->getNow() . "] [$location] " . $m . "\n");
	}

	/**
	 *	Logs the given error message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eError($location, $e) {
		$this->writeToLogFile('[ERROR] [' . $this->getNow() . "] [$location] " . $e . "\n");
		$this->writeToErrorFile('[' . $this->getNow() . "] [$location] " . $e . "\n");		
	}

	/**
	 *	Logs the given warning message from the given location. The location
	 *	could be class::method, file:linenumber or anything you want.
	 */
	public function eWarning($location, $w) {
		$this->writeToLogFile('[WARNING] [' . $this->getNow() . "] [$location] " . $w . "\n");
		$this->writeToWarningFile('[' . $this->getNow() . "] [$location] " . $w . "\n");		
	}
	
}
