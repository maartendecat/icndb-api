<?php
/**
 *	Simple helper class for the number of requests per hour on a certain date.
 */
class StatPerHour extends StatPerDay{

	/**
	 *	Construct this new StatPerHour with given values.
	 */
	public function __construct($year,$month,$day,$hour,$value) {
		parent::__construct($year,$month,$day,$value);
		$this->hour = $hour;
	}

	private $hour;
	public function getHour() {
		return $this->hour;
	}
}
