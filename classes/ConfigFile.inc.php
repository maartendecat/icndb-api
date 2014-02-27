<?php
/**
 *	Abstract superclass for config files. Implements common functionality.
 */
abstract class ConfigFile {

	protected $logger;

	/**
	 *	Construct this new ConfigFile with given logger.
	 *	
	 *	@param	$logger	The logger this object logs to. If not given, nothing is logged.
	 */
	public function __construct($logger = null) {
		if($logger == null) {
			// insert a new dummy logger object so $this->logger can _always_ be used.
			$this->logger = new DummyLogger();
		} else {
			$this->logger = $logger;
		}
	}

	/**
	 *	Returns whether the given line from the config file should be ignored.
	 *	A line should be ignored if it only exists of spaces or tabs, is empty,
	 *	or is a comment.
	 */
	public final function shouldIgnore($line) {
		return trim($line) == '' || $this->isComment($line);
	}
		
	/**
	 *	Returns whether the given line is a comment or not. 
	 *	A line is a comment if the first non-space, non-tab character
	 *	is '#' or ';'.
	 */
	public final function isComment($line) {
		$trimmed = trim($line);
		return preg_match('/^#/',$trimmed) || preg_match('/^;/',$trimmed);
	}

	/**
	 *	Returns a string representation of this ConfigFile object.
	 */
	abstract public function toString();
}
