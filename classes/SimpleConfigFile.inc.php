<?php
/**
 *	Class used for representing simple config files. A simple config file 
 *	consists only of key-value pairs, not groups.
 */
class SimpleConfigFile extends ConfigFile {

	/**
	 *	Construct this new ConfigFile with given config file to read the
	 *	parameters from. The config file is immediately parsed. Comments and empty lines are ignored.
	 *	
	 *	@param 	$file	The relative address of the config file to be read.
	 *	@param	$logger	The logger this object logs to. If not given, nothing is logged.
	 */
	public function __construct($file,$logger=null) {
		parent::__construct($logger);

		$fp = fopen($file, "r");

		while (!feof($fp)) {
			$line = trim(fgets($fp));
			if ($line &&  !$this->shouldIgnore($line)) {
				if (preg_match('/^(.*)=(.*)$/',$line,$matches)) {
					// a normal group
					$option = trim($matches[1]);
					$value = trim($matches[2]);
					$this->set($option, $value);
				} else {
					// not a comment or option, log an error
					echo('[' . __METHOD__ . "] Incorrect line: $line\n");
					$this->logger->eWarning(__METHOD__,"Incorrect line: $line");
				}
			}
		}
		fclose($fp);
	}

	private $options = array();

	/**
	 *	Sets the value of the option called $option to
	 *	$value. Could overwrite existing options.
	 */
	private function set($option, $value) {
		$this->options[$option] = $value;
	}

	/**
	 *	Returns the value of the option called $option.
	 */
	public function get($option) {
		return $this->options[$option];
	}

	/**
	 *	Returns a string representation of this SimpleConfigFile object.
	 */
	public function toString() {
		$toReturn = '';
		foreach($this->options as $option=>$value) {
			$toReturn .= "$option = $value\n";
		}
		return $toReturn;
	}
}
