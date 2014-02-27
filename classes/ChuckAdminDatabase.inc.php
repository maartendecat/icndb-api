<?php
/**
 *	Class used for representing the possible queries for the admin section of the application.
 *	The main purporse of this class is to leave the public API unharmed when writing new admin
 *	code. This class only adds new queries to the database, for the rest, it operates the same.
 */
class ChuckAdminDatabase extends ChuckDatabase {

	/**
	 *	Does exactly the same as ChuckDatabase::__construct:
	 *
	 *	Constructs this new database with given parameters and connects to the database.
	 *	Note: To connect on a different port than 3307, use $dbhost="host:port".
	 *
	 *	@throws DatabaseConnectionException
	 *			If connecting to the database with given arguments failed.
	 */
	public function __construct($dbhost, $dbname, $dbuser, $dbpass, $prefix) {
		parent::__construct($dbhost, $dbname, $dbuser, $dbpass,$prefix);
	}

	/***************************************************
	 *	Quotes
	 ***************************************************/	

	/**
	 *	Deletes the quote with given id and its category associations from the database. 
	 *	Deletes nothing if no such quote exists.
	 */
	public function deleteQuote($id) {
		$this->query("DELETE FROM `" . $this->addPrefix('quotes') . "` WHERE `id` = '$id';");
		$this->query("DELETE FROM `" . $this->addPrefix('quote2category') . "` WHERE `quoteId` = '$id';");
	}

	/**
	 *	Inserts a new quote with given quote text.
	 *	Returns the new quote (with its new id).
	 *	Initially, the quote belongs to no categories.
	 *	
	 *	@param	$quote	String
	 *	@return	Quote instance with its new id set.
	 */
	public function createQuote($quote) {
		$quote = $this->cleanUpQuote($quote);
		$this->query('INSERT INTO `' . $this->addPrefix('quotes') . "` (`id`, `quote`) VALUES (NULL ,  '$quote');");
		$newQuoteId = mysql_insert_id();
		return new Quote($newQuoteId, $quote);
	}

	/**
	 *	Cleans up the quotes in the database.
	 */
	public function cleanUpQuotes() {	
		echo("*** starting update\n");	
		$quotes = $this->getAllQuotes();
		foreach($quotes as $quote) {
			$before = $quote->getQuote();
			$quote->setQuote($this->cleanUpQuote($quote->getQuote()));
			$this->updateQuote($quote);
			$after = $quote->getQuote();
			if($before != $after) {
				echo("Updated quote #" . $quote->getId() . ": [$before] -> [$after]<br>\n");
			} else {
				echo("Quote #" . $quote->getId() . " remained unchanged<br>\n");
			}
		}
		echo("*** update finished\n");
	}

	/**
 	 *	Updates the given quote in the database.
	 *	
	 *	@param	$quote	A Quote instance
	 */
	public function updateQuote($quote) {
		$id = $quote->getId();
		$q = $this->cleanUpQuote($quote->getQuote());
		$this->query('UPDATE `' . $this->addPrefix('quotes') . "` SET `quote`='$q' WHERE `id`='$id';");
		$categories = $this->getCategories();
		foreach($categories as $c) {
			if($quote->belongsToCategory($c)) {
				$this->query("INSERT INTO `" . $this->addPrefix('quote2category') . "` (`quoteId`,`categoryId`) SELECT '$id', `id` FROM `" . $this->addPrefix('categories') . "` WHERE `name`='$c'");
			} else {
				$this->query("DELETE `" . $this->addPrefix('quote2category') . "` FROM `" . $this->addPrefix('quote2category') . "` INNER JOIN `" . $this->addPrefix('categories') . "` WHERE `quoteId`='$id' AND `name`='$c' AND `categoryId`=`id`;");
			}
		}				
	}

	/**
	 *	Cleans up the given quote and returns the result.
	 *
	 *	SHOULD ONLY BE CALLED BEFORE INSERTING A QUOTE IN THE DATABASE
	 *	LEADS TO TOO MUCH BACKSLASHES OTHERWISE
	 *
	 *	@param	$quote	String
	 *	@return	String
	 */
	protected function cleanUpQuote($quote) {
		$quote = trim($quote);

		//$quote = str_replace('\\', '', $quote);

		$quote = str_replace("\n", '', $quote);
		$quote = str_replace("\t", '', $quote);
		$quote = str_replace("\r", '', $quote);
		$quote = str_replace("\b", '', $quote);
		$quote = str_replace('’', "'", $quote);
		$quote = str_replace('`', "'", $quote);

		// replace smart quotes (curly quotes) [http://shiflett.org/blog/2005/oct/convert-smart-quotes-with-php]
		$quote = str_replace(chr(145), "'", $quote);
		$quote = str_replace(chr(146), "'", $quote);
		$quote = str_replace(chr(147), '"', $quote);
		$quote = str_replace(chr(148), '"', $quote);
		$quote = str_replace(chr(151), '-', $quote);
		
		$quote = addslashes($quote);

		$quote = str_replace('Chuck', '%firstName%', $quote);
		$quote = str_replace('Norris', '%lastName%', $quote);
		return $quote;
	}

	/***************************************************
	 *	Stats
	 ***************************************************/

	/**
	 *	Returns the number of requests grouped per day.
	 *	The result is an array containing StatPerDay objects.
	 *
	 *	@param	$descending	Whether the results will be ordered in descending order
	 *				(most recent stat first) or not. 
	 *			Default: false
	 */
	public function getNbRequestsPerDay($descending = false) {
		$order = 'ASC';
		if($descending) {
			$order = 'DESC';
		}
		$result = $this->query('SELECT year(a.date) AS year, month(a.date) AS month, day(a.date) AS day, count(a.id) AS count FROM `' . $this->addPrefix('requestsLog') . "` AS a GROUP BY year, month, day ORDER BY year $order, month $order, day $order;");
		$toReturn = array();
		while ($row = mysql_fetch_assoc($result)) {
			array_push($toReturn, new StatPerDay($row['year'], $row['month'], $row['day'], $row['count']));
		}
		return $toReturn;
	}

	/**
	 *	Returns the number of requests grouped per hour.
	 *	The result is an array containing StatPerHour objects.
	 *
	 *	@param	$descending	Whether the results will be ordered in descending order
	 *				(most recent stat first) or not. 
	 *			Default: false
	 */
	public function getNbRequestsPerHour($descending = false) {
		$order = 'ASC';
		if($descending) {
			$order = 'DESC';
		}
		$result = $this->query('SELECT year(a.date) AS year, month(a.date) AS month, day(a.date) AS day, hour(a.date) AS hour, count(a.id) AS count FROM `' . $this->addPrefix('requestsLog') . "` AS a WHERE 1 GROUP BY year, month, day, hour ORDER BY year $order, month $order, day $order, hour $order;");
		$toReturn = array();		
		while ($row = mysql_fetch_assoc($result)) {
			array_push($toReturn, new StatPerHour($row['year'], $row['month'], $row['day'], $row['hour'], $row['count']));
		}
		return $toReturn;
	}
}
