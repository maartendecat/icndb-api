<?php
/**
 *	Class used for representing the Chuck database and its possible queries.
 *  Note 2012-11-09: refactored from previous version to *not* use a real database.
 */
class ChuckDatabase {

	/**
	 *	Constructs this new database and fills it up.
	 */
	public function __construct() {
    ChuckDatabaseFiller::fill($this);
	}

  /**
   *  The categories array.
   */
  private $categories = array();

  /**
   *  Add a category to the db.
   *
   *  @param  $category Category object.
   */
  public function addCategory($category) {
    $this->categories[$category->getId()] = $category;
  }

  /**
   *  The jokes array.
   */
  private $jokes = array();

  /**
   *  Add a joke to the db.
   *
   *  @param  $joke Joke object.
   */
  public function addJoke($joke) {
    $this->jokes[$joke->getId()] = $joke;
  }

	/***************************************************
	 *	Helper functions: Select quotes from id arrays.
	 ***************************************************/



  // ================================

	/**
	 *	Returns all quotes from the given array of quote id's.
	 *	
	 *	@param	$ids	Array of ints to choose from.
	 *	@return	Array of Quote instances.
	 *	
	 *	@pre	The id's in $ids are all valid id's of existing quotes in the database.
	 */
	private function selectAllQuotes($ids) {
		$result = array();
		foreach($ids as $id) {
			try {
				array_push($result, $this->getQuote($id));
			} catch (NoSuchQuoteException $e) {
				exit('[' . __METHOD__ . '] There should NEVER be an exception here...');
			}
		}
		return $result;
	}

	/***************************************************
	 *	Jokes
	 ***************************************************/	

	/**
	 *	Returns an array containing all the quotes in the database.
	 *
	 *	@return	Array of Quote instances
	 */
	public function getAllQuotes() {
		return $this->jokes;
	}

	/**
	 *	Returns the joke with given id. Throws a NoSuchQuoteException is no
	 *	such quote exists.
	 *	
	 *	@return	Joke instance
	 */
	public function getQuote($id) {
		if(array_key_exists($id, $this->jokes)) {
      return $this->jokes[$id];
    }
		throw new NoSuchQuoteException("No quote with id=$id.");
	}

  /**
	 *	Helper functions. Returns a random joke from the given array of jokes.
	 *	
   *  @param  $jokes  Array of Quotes
	 *	@return	Joke instance
	 */
	private function selectRandomQuote($jokes) {
		return $jokes[array_rand($jokes)];
	}

	/**
	 *	Returns a random joke from the db.
	 *	
	 *	@return	Joke instance
	 */
	public function getRandomQuote() {
		return $this->selectRandomQuote($this->jokes);
	}

  /**
	 *	Returns the given number of random quotes from the given array of quotes.
	 *	If $number exceeds count($quotes), the number of returned quotes is
	 *	count($quotes).
	 *	
	 *	@param	$number	The number of quotes to return.
   *  @param  $quotes Array of Auote instances.
	 *	@return	Array of Quote instances.
	 */
	public function selectRandomQuotes($number, $quotes) {
		$countJokes = count($quotes);
		// boundary check
		if($number > $countJokes) {
			$number = $countJokes;
		}
    $collection = $quotes; // assignment by copy
		$result = array();
		while(count($result) < $number) {
      $new = $this->selectRandomQuote($collection);
      // add $new to result
      array_push($result, $new);
      // remove $new from collection
      foreach($collection as $key=>$value) {
        if($new->equals($value)) {
          unset($collection[$key]);
          break;
        }
      }
		}
		return $result;
	}

  /**
	 *	Returns the given number of random quotes from the database.
	 *	If $number exceeds count($this->jokes), the number of returned quotes is
	 *	count($this->jokes).
	 *	
	 *	@param	$number	The number of quotes to return.
	 *	@return	Array of Quote instances.
	 */
	public function getRandomQuotes($number) {
		return $this->selectRandomQuotes($number, $this->jokes);
	}

	/**
	 *	Returns an array containing all the quotes in the database that belong to one of the
	 *	given categories. Returns the given number of general random quotes if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$categories	Array of strings
	 *	@return	Array of Quote instances
	 */
	public function getAllQuotesBelongingTo($categories) {
    return array_filter($this->jokes, function($item) use ($categories) {
      return $item->belongsToOneOfCategories($categories);
    });
	}

	/**
	 *	Returns an array containing all the quotes in the database that do _NOT_ belong to one of the
	 *	given categories. Returns a general random quote if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$categories	Array of strings
	 *	@return	Array of Quote instances
	 *
	 *	@return	Array of Quote instances
	 */
	public function getAllQuotesExcluding($categories) {
		return array_filter($this->jokes, function($item) use ($categories) {
      return !$item->belongsToOneOfCategories($categories);
    });
	}

	/**
	 *	Returns a random quote from the database which belongs to one of the
	 *	given categories. Returns a general random quote if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$categories	Array of strings
	 *	@return	Quote instance
	 */
	public function getRandomQuoteBelongingTo($categories) {
		if($categories == null || count($categories) == 0) {
			return $this->getRandomQuote();
		}
		return $this->selectRandomQuote($this->getAllQuotesBelongingTo($categories));
	}

	/**
	 *	Returns the given number of random quotes from the database that belong to one of the
	 *	given categories. Returns the given number of general random quotes if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$number	The number of quotes to return.
	 *	@param	$categories	Array of strings
	 *	@return	Array of Quote instances
	 */
	public function getRandomQuotesBelongingTo($number, $categories) {
		if($categories == null || count($categories) == 0) {
			return $this->getRandomQuote();
		}
		return $this->selectRandomQuotes($number, $this->getAllQuotesBelongingTo($categories));
	}

	/**
	 *	Returns a random quote from the database that does _NOT_ belong to one of the
	 *	given categories. Returns a general random quote if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$categories	Array of strings
	 *	@return	Quote instance
	 */
	public function getRandomQuoteExcluding($categories) {
		if($categories == null || count($categories) == 0) {
			return $this->getRandomQuote();
		}
		return $this->selectRandomQuote($this->getAllQuotesExcluding($categories));
	}

	/**
	 *	Returns the given number of random quote from the database that do _NOT_ belong to one of the
	 *	given categories. Returns a general random quote if no categories are
	 *	given ($categories == null || count($categories) == 0).
	 *
	 *	@param	$number	The number of quotes to return.
	 *	@param	$categories	Array of strings
	 *	@return	Array of Quote instances
	 */
	public function getRandomQuotesExcluding($number, $categories) {
		if($categories == null || count($categories) == 0) {
			return $this->getRandomQuote();
		}
		return $this->selectRandomQuotes($number, $this->getAllQuotesExcluding($categories));
	}

	/**
	 *	Returns the number of quotes in the database.
   *
	 *	TODO implement methods to get the number of jokes belonging to a certain category of excluding certain categories.
   *  
   *  @return int
	 */
	public function getQuoteCount() {
		return count($this->jokes);
	}

	/***************************************************
	 *	Categories
	 ***************************************************/

	/**
	 *	Returns the names of the categories in the database.
	 *
	 *	@return	Array of strings
	 */
	public function getCategories() {
		$result = array();
    foreach($this->categories as $c) {
      array_push($result, $c->getName());
    }
    return $result;
	}

	/**
	 *	Returns whether the given category exists in the system.
	 *
	 *	@param	$category	String
	 *	@return	boolean
	 */
	public function isValidCategory($category) {
		$categories = $this->getCategories();
		foreach($categories as $c) {
			if($c == $category) {
				return true;		
			}
		}
		return false;
	}

	/**
	 *	Returns the categories the quote with given id belongs to.
	 *
	 *	@return	Array of strings
	 */
	protected function getCategoriesForQuote($id) {
		$result = array();
    foreach($this->getQuote($id)->getCategories() as $c) {
      array_push($result, $c->getName());
    }
    return $result;
	}

	/**
	 *	Adds the categories of the quote to the quote instance.
   *  TODO doen we niet meer zeker?
	 *
	 *	@param	$quote	Quote instance
	 */
	protected function addCategories($quote) {
		$categories = $this->getCategoriesForQuote($quote->getId());
		foreach($categories as $c) {
			$quote->addCategory($c);
		}
	}
}
