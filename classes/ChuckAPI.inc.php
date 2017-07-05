<?php
/**
 *	Singleton class used for representing the Chuck API.
 *  Note 2012-11-09: refactored from previous version to *not* use a real database,
 *    config file or log files.
 */
class ChuckAPI {

	/**
	 *	Singleton instance.
	 */
	private static $instance;
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new ChuckAPI();
		}
		return self::$instance;
	}

	/**
	 *	The databse for this ChuckAPI instance.
	 */
	protected $database;

	/**
	 *	The logger for this ChuckAPI instance.
	 */
	private $logger;

  /**
   *  The timer for this ChuckAPI instance: times from the construction of the object.
   */
  private $timer;

  /**
   *  The escaper for this ChuckAPI instance. This will be used for escaping quotes.
   */
  private $escaper;

  protected function getEscaper() {
    return $this->escaper;
  }

  /**
   *  Sets the escaper for this ChuckAPI instance.
   *
   *  @param  $escaper  Escaper instance.
   */
  public function setEscaper($escaper) {
    $this->escaper = $escaper;
  }
	
	/**
	 *	Construct this new ChuckAPI instance: construct the Chuck database, logger and escaper.
   *  The default escaper is an HTMLEscaper.
	 */
	protected function __construct() {
		$this->database = new ChuckDatabase();
		$this->logger = new SysoutLogger();
    $this->timer = new Timer();
    $this->escaper = new HTMLEscaper();
	}

	/************************************************************************************
	 *	OLD API
	 ************************************************************************************/
		
	/**
	 *	OLD API WITH GET PARAMETERS:
 	 *	----------------------------
	 *	
	 *	Processes the current request (present in $_POST and/or $_GET).
	 *	The result of the request is printed as JSON data and is of the format:
	 *	{ type: T, value: V} with T the type of the response (and exception class
	 *	or 'success') and V the actual returned value (the exception, a list of quotes,
	 *	a quote, etc). If a prename and surname are given, 'Chuck' and 'Norris' are replaced
	 *	in the quote.
	 *	
	 *	Syntax:
	 *	f: the function called on the API 
	 *	|->	rand: return a random quote.
	 *	|		Other needed arguments: none
	 *	|->	q: return a quote by id	
	 * 	|		Other needed arguments: 
	 *	|		|->	id: the id of the needed quote
	 *	|		Throws: NoSuchQuoteException if no quote with the given id exists.
	 *	fn: the first name to replace 'Chuck' with
	 *	ln: the last name to replace 'Norris' with
	 */
	public function processGETAPI() {
		// process the request
		if(!isset($_GET['f'])) {
			return;
		}

		if($_GET['f'] == 'rand') {
			$quote = $this->database->getRandomQuote();
			// replace names if asked for. Default: 'Chuck Norris'
			if(isset($_GET['fn']) && isset($_GET['ln'])) {
				$quote->replaceNames($_GET['fn'], $_GET['ln']);
			} else {
				$quote->replaceNames('Chuck', 'Norris');
			}
			// return result
			// don't use Quote::toJSON(), this uses "joke" instead of "quote"
			// as defined in the new API
			echo('{ "type": "success", "value": { "id": ' . $quote->getId() . ', "quote": "' . htmlspecialchars($quote->getQuote()) . '" } }');
			return;
		}
		
		if($_GET['f'] == 'q') {
			if(!$_GET['id']) {
				return;
			}
			$id = $_GET['id'];
			try {
				$quote = $this->database->getQuote($id);
				if($_GET['fn'] && $_GET['ln']) {
					$quote->replaceNames($_GET['fn'], $_GET['ln']);
				}
				// don't use Quote::toJSON(), this uses "joke" instead of "quote"
				// as defined in the new API
				echo('{ "type": "success", "value": { "id": ' . $quote->getId() . ', "quote": "' . htmlspecialchars($quote->getQuote()) . '" } }');
				return;
			} catch (NoSuchQuoteException $e) {
				echo('{ "type": "NoSuchJokeException", "value": ' . $e->toJSON() . ' }');
				return;
			}
		}
	}

	/************************************************************************************
	 *	SINGLE QUOTE METHODS
	 ************************************************************************************/

  /**
   *  Returns the debug info for this API result.
   *  Empty string is returned if ?debug is not set, valid JSON fragment for concatenation
   *  later on is returned otherwise.
   */
  protected function getDebugJSON() {
    if(isset($_GET['debug'])) {
      return ', "debug": { "duration": { "value": ' . $this->timer->tock() . ', "unit": "ms" } }';
    }
    return '';
  }

	/**
	 *	Echos the given quote as a JSON API result.
	 *	
	 *	@param	$quote	Quote instance
	 */
	protected function echoQuote($quote) {
		echo('{ "type": "success", "value": ' . $quote->toJSON($this->escaper) . $this->getDebugJSON() . ' }');	
	}

	/**
	 *	Echos the given exception as a JSON API result.
	 *	
	 *	@param	$quote	Quote instance
	 */
	protected function echoException($e) {
		echo('{ "type": "' . get_class($e) . '", "value": "' . $e->getMessage() . '"' . $this->getDebugJSON() . ' }');	
	}

	/**
	 *	Echos the quote with given id. Echos the NoSuchQuoteException
	 *	if no such quote exists.
	 *
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$id		The id of the quote to echo.
	 */
	public function echoQuoteById($fn, $ln, $id) {
		try {
			$quote = $this->database->getQuote($id);
			$quote->replaceNames($fn, $ln);
			$this->echoQuote($quote);
		}  catch (NoSuchQuoteException $e) {
			$this->echoException($e);
		}
	}

	/**
	 *	Echos a random quote with first name and last name as given.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 */
	public function echoRandomQuote($fn, $ln) {
		$quote = $this->database->getRandomQuote();
		$quote->replaceNames($fn, $ln);
		$this->echoQuote($quote);
	}

	/**
	 *	Echos a random quote with first name and last name as given.
	 *	Quotes from one of the given categories are excluded.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$categories	The categories to exclude (array of strings)
	 */
	public function echoRandomQuoteExcluding($fn, $ln, $categories) {
		$quote = $this->database->getRandomQuoteExcluding($categories);
		$quote->replaceNames($fn, $ln);
		$this->echoQuote($quote);		
	}

	/**
	 *	Echos a random quote with first name and last name as given.
	 *	The quote is guaranteed to belong to one of the given categories
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$categories	The categories to limit (array of strings)
	 */
	public function echoRandomQuoteBelongingTo($fn, $ln, $categories) {
		$quote = $this->database->getRandomQuoteBelongingTo($categories);
		$quote->replaceNames($fn, $ln);
		$this->echoQuote($quote);		
	}

	/************************************************************************************
	 *	MULTIPLE QUOTE METHODS
	 ************************************************************************************/

	/**
	 *	Echos the given array of quotes as a JSON API result.
	 *
	 *	@param	$quotes	An array of Quote instance
	 *  @param  $offset An integer representing a 'page index'
	 *  @param  $limit An integer to limit the result list
	 */
	protected function echoQuotes($quotes) {
		$array = '';
		$first = true;

		foreach($quotes as $quote) {
			if($first == false) {
				$array .= ', ';
			}
			$array .= $quote->toJSON($this->escaper);
			$first = false;
		}
		echo('{ "type": "success", "value": [ ' . $array . ' ] ' . $this->getDebugJSON() . ' }');
	}

	/**
	 *	Echos all the quotes in the database with first name and last name as given.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 */
	public function echoAllQuotes($fn, $ln, $offset=-1, $limit=-1) {
		$quotes = $this->database->getAllQuotes($offset, $limit);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
	}

	/**
	 *	Echos all the quotes in the database with first name and last name as given.
	 *	The quote is guaranteed to belong to one of the given categories.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$categories	The categories to limit (array of strings)
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 */
	public function echoAllQuotesBelongingTo($fn, $ln, $categories, $offset=-1, $limit=-1) {
		$quotes = $this->database->getAllQuotesBelongingTo($categories, $offset, $limit);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
	}

	/**
	 *	Echos all the quotes in the database with first name and last name as given.
	 *	Quotes from one of the given categories are excluded.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$categories	The categories to exclude (array of strings)
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 */
	public function echoAllQuotesExcluding($fn, $ln, $categories, $offset=-1, $limit=-1) {
		$quotes = $this->database->getAllQuotesExcluding($categories, $offset, $limit);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
	}

	/**
	 *	Echos the given number of random quotes with first name and last name as given.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$number	The number of random quotes to be echoed.
	 */
	public function echoRandomQuotes($fn, $ln, $number) {
		$quotes = $this->database->getRandomQuotes($number);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
	}

	/**
	 *	Echos the given number of random quotes with first name and last name as given.
	 *	Quotes from one of the given categories are excluded.
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$number	The number of random quotes to be echoed.
	 *	@param	$categories	The categories to exclude (array of strings)
	 */
	public function echoRandomQuotesExcluding($fn, $ln, $number, $categories) {
		$quotes = $this->database->getRandomQuotesExcluding($number, $categories);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
	}

	/**
	 *	Echos the given number of random quotes with first name and last name as given.
	 *	The quote is guaranteed to belong to one of the given categories
	 *	
	 *	@param	$fn 	First name (string)
	 *	@param	$ln		Last name (string)
	 *	@param	$number	The number of random quotes to be echoed.
	 *	@param	$categories	The categories to limit (array of strings)
	 */
	public function echoRandomQuotesBelongingTo($fn, $ln, $number, $categories) {
		$quotes = $this->database->getRandomQuotesBelongingTo($number, $categories);
		foreach($quotes as $quote) {
			$quote->replaceNames($fn, $ln);
		}
		$this->echoQuotes($quotes);
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
		echo('{ "type": "success", "value": [ ' . $array . ' ] }');
	}	

	/************************************************************************************
	 *	JOKES COUNT
	 ************************************************************************************/

	/**
	 *	Echos the number of quotes in the system.
	 */
	public function echoQuoteCount() {
		$count = $this->database->getQuoteCount();
		echo("{ \"type\": \"success\", \"value\": $count }");
	}
}
