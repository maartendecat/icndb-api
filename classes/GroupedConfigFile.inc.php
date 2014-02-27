<?php
/**
 *	Class used for representing grouped config files. A config file 
 *	consists only of grouped key-value pairs.
 */
class GroupedConfigFile extends ConfigFile {

	/**
	 *	Variable representing whether the parsing was succesfull or not.
	 */
	private $parsingOK = TRUE;
	public function parsingOK() {
		return $this->parsingOK;
	}

	/**
	 *	Construct this new GroupedConfigFile with given config file to read the
	 *	parameters from. The config file is immediately parsed. Comments and empty lines are ignored.
	 *	
	 *	@param 	$file	The relative address of the config file to be read.
	 *	@param	$logger	The logger this object logs to. If not given, nothing is logged.
	 */
	public function __construct($file,$logger=null) {
		parent::__construct($logger);
		
		$comment = "#";

		$fp = fopen($file, "r");
		if(! $fp) {
			exit('[' . __METHOD__ . '] File ' . $file . ' could not be opened.');
		}

		$currentGroup = 'DEFAULT';
		while (!feof($fp)) {
			$line = trim(fgets($fp));
			if ($line && !$this->shouldIgnore($line)) {
				if(preg_match('/^\[(.*)\]$/',$line,$matches)) {
					// new group declaration
					$currentGroup = trim($matches[1]);
				} else if (preg_match('/^(.*)=(.*)$/',$line,$matches)) {
					// a normal group
					$option = trim($matches[1]);
					$value = trim($matches[2]);
					$this->set($currentGroup, $option, $value);
				} else {
					// not a comment, group declaration or option, log an error
					echo('[' . __METHOD__ . "] Incorrect line: $line\n");
					$this->logger->eWarning(__METHOD__,"Incorrect line: $line");
					$this->parsingOK = FALSE;
				}
			}
		}
		fclose($fp);
	}

	private $options = array();

	/**
	 *	Sets the value of the option called $option in the group called $group 
	 *	to	$value. Could overwrite existing options.
	 */
	private function set($group, $option, $value) {
		$this->options[$group][$option] = $value;
	}

	/**
	 *	Returns the value of the option called $option in the group called $group.
	 */
	public function get($group, $option) {
		return $this->options[$group][$option];
	}

	/**
	 *	Returns an array containing all the options from the group called $group.
	 */
	public function getGroup($group) {
		return $this->options[$group];
	}

	/**
	 *	Returns a string representation of this SimpleConfigFile object.
	 */
	public function toString() {
		$toReturn = '';
		foreach($this->options as $group => $options) {
			$toReturn .= "[$group]\n";
			foreach($options as $option => $value) {
				$toReturn .= "$option=$value\n";
			}
		}
		return $toReturn;
	}
}
