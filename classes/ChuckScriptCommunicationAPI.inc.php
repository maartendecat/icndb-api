<?php
/**
 *	Singleton class used for representing the Chuck API that can be used in script communication.
 */
class ChuckScriptCommunicationAPI extends ChuckAPI {

	/**
	 *	Singleton instance.
	 */
	private static $instance;
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new ChuckScriptCommunicationAPI();
		}
		return self::$instance;
	}

	/**
	 *	Callback function (String)
	 */
	private $callback;
	public function setCallback($callback) {
		$this->callback = $callback;
	}
	
	/**
	 *	Construct this new ChuckAPI instance.
	 */
	protected function __construct() {
		parent::__construct();
		$this->callback = null;
	}

	/************************************************************************************
	 *	SINGLE QUOTE METHODS
	 ************************************************************************************/

	/**
	 *	Echos the given quote as a JSON API result.
	 *	
	 *	@param	$quote	Quote instance
	 *
	 *	@override
	 */
	protected function echoQuote($quote) {
		echo('(function() { ' . $this->callback . '({ "type": "success", "value": ' . $quote->toJSON($this->getEscaper()) . ' }); })();');	
	}

	/**
	 *	Echos the given exception as a JSON API result.
	 *	
	 *	@param	$quote	Quote instance
	 */
	protected function echoExeption($e) {
		echo('(function() { ' . $this->callback . '({ "type": "' . get_class($e) . '", "value": "' . $e->getMessage() . '" }); })();');	
	}

	/**
	 *	Echos the given array of quotes as a JSON API result.
	 *
	 *	@param	$quotes	An array of Quote instance
	 */
	protected function echoQuotes($quotes) {
		$array = '';
		$first = true;
		foreach($quotes as $quote) {
			if($first == false) {
				$array .= ', ';
			}
			$array .= $quote->toJSON($this->getEscaper());
			$first = false;
		}
		echo('(function() { ' . $this->callback . '({ "type": "success", "value": [ ' . $array . ' ] }); })();');
	}

	/************************************************************************************
	 *	CATEGORIES
	 ************************************************************************************/

	/**
	 *	Echos the categories in the system.
	 */
	public function echoCategories() {
		$categories = $this->database->getCategories();
		$array = '';
		$first = true;
		foreach($categories as $category) {
			if(!$first) {
				$array .= ', ';
			}
			$array .= "\"$category\"";
			$first = false;
		}
		echo('(function() { ' . $this->callback . '({ "type": "success", "value": [ ' . $array . ' ] }); })();');
	}	

	/************************************************************************************
	 *	JOKES COUNT
	 ************************************************************************************/

	/**
	 *	Echos the number of quotes in the system.
	 */
	public function echoQuoteCount() {
		$count = $this->database->getQuoteCount();
		echo("(function() { " . $this->callback . "({ \"type\": \"success\", \"value\": $count }); })();");
	}
}
