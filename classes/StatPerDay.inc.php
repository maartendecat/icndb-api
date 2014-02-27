<?php
/**
 *	Simple helper class for the number of requests per day on a certain date.
 */
class StatPerDay {

	/**
	 *	Construct this new StatPerDay with given values.
	 */
	public function __construct($year,$month,$day,$value) {
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
		$this->value = $value;
	}

	private $year;
	public function getYear() {
		return $this->year;
	}

	private $month;
	public function getMonth() {
		return $this->month;
	}

	private $day;
	public function getDay() {
		return $this->day;
	}

	private $value;
	public function getValue() {
		return $this->value;
	}

	/**
	 *	Returns the date as a string.
	 */
	public function getDate() {
		return $this->getYear() . '-' . $this->getMonth() . '-' . $this->getDay();
	}
}
