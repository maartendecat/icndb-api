<?php
/**
 *	Class used for representing a timer that times a single event at a time.
 *
 *	Use:
 *		$timer = new Timer();
 *		...
 *		$period = $timer->tock();
 */
class Timer {

	/**
 	 *	Start time in seconds (float).
	 */
	private $start;
	
	/**
	 *	Initialize this new timer. Immediately tick()s.
	 */
	public function __construct() {
		$this->tick();
	}

	/**
	 *	Sets the internal startup time to the current time.
	 */
	public function tick() {
		list($usec, $sec) = explode(" ", microtime());
		$this->start = $sec + $usec;
	}

	/**
	 *	Returns the current period since the last tick() in milliseconds.
	 */
	public function tock() {
		list($usec, $sec) = explode(" ", microtime());
		$current = $sec + $usec;
		return round(($current-$this->start)*1000,3);
	}
}
