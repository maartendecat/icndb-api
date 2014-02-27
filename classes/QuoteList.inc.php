<?php
/**
 *	Class used for representing a list of quotes. Use this class instead of a 
 *	normal array if you want to convert it to JSON later on.
 */
class QuoteList {
	
	private $list = array();

	/**
	 *	Initialize this new QuoteList (initially emtpy).
	 */
	public function __construct() {}

	/**
	 *	Append a quote to the list.
	 */
	public function append($quote) {
		array_push($this->list, $quote);
	}
		
	/**
	 *	Returns a JSON representation of this quote list.
	 */
	public function toJSON() {
		$json = '[ ';
		foreach($this->list as $quote) {
			$json .= $quote->toJSON() . ', ';
		}
		// remove the last colon
		preg_match('/(.*), $/', $json, $matches);
		$json = $matches[1];
		$json .= ' ]';
		return $json;
	}

	/**
 	 *	Returns an iterator for this quote list.
	 */
	public function getIterator() {
		return $this->list;
	}
}
