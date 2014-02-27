<?php
/**
 *	Class used for representing a quote in the system.
 */
class Quote {

  /**
   *
   */
  public function __construct($id, $quote, $categories) {
    $this->id = $id;
    $this->quote = $quote;
    $this->categories = $categories;
  }

	/**
	 *	Public constant values.
	 */
//	const CATEGORY_EXPLICIT = 'explicit';
//	const CATEGORY_NERDY = 'nerdy';
	
	private $id;
	public function getId() {
		return $this->id;
	}

	private $quote;
	public function getQuote() {
		return $this->quote;
	}
	public function setQuote($quote) {
		$this->quote = $quote;
	}

//	private $explicit;
//	public function isExplicit() {
//		return $this->explicit;	
//	}
//	public function setIsExplicit($isExplicit) {
//		$this->explicit = $isExplicit;
//	}

//	private $nerdy;
//	public function isNerdy() {
//		return $this->nerdy;	
//	}
//	public function setIsNerdy($isNerdy) {
//		$this->nerdy = $isNerdy;
//	}

	/***************************************************
	 *	Categories
	 ***************************************************/

	private $categories;

	/**
	 *	Adds the given category	to the list of categories this quote belongs to.
	 *	Does not add double entries in the list of categories.
	 *
	 *	@param	$category	String
	 */
	public function addCategory($category) {
		// check for doubles
		foreach($this->categories as $c) {
			if($c == $category) {
				return;
			}
		}
		array_push($this->categories, $category);
	}

	/**
	 *	Removes the given category from the list of categories this quote belongs to.
	 *	Removes nothing if this quote does not belong to the given category.
	 */
	public function removeCategory($category) {
		foreach($this->categories as $key => $value) {
			if($value == $category) {
				unset($this->categories[$key]);
			}
		}
	}

	/**
	 *	Returns whether this quote belongs to the given category.
   *  Comparison by category name.
	 *
	 *	@param	$category	String
	 */
	public function belongsToCategory($category) {
		foreach($this->categories as $c) {
			if($c->getName() == $category) {
				return true;
			}
		}
		return false;
	}

	/**
	 *	Returns whether this quote belongs one of the given categories.
   *  Comparison by category name.
	 *
	 *	@param	$category	Array of Strings
	 */
	public function belongsToOneOfCategories($categories) {
		foreach($categories as $c) {
			if($this->belongsToCategory($c)) {
				return true;
			}
		}
		return false;
	}

	/**
	 *	Returns the categories this quote belongs to.
	 *	
	 *	@return	Array of Strings
	 */
	public function getCategories() {
		// copy array to ensure encapsulation (not sure this is needed, php = copy by value for arrays)
		$cs = array();
		foreach($this->categories as $c) {
			array_push($cs, $c);
		}
		return $cs;
	}

	/**
	 *	Replaces all occurrences of %firstName% and %lastName%
	 *	with the respective given values.
	 */
	public function replaceNames($firstName, $lastName) {
		$this->quote = str_replace('%firstName[0]%', substr($firstName, 0, 1), $this->quote);
		$this->quote = str_replace('%lastName[0]%', substr($lastName, 0, 1), $this->quote);
		$this->quote = str_replace('%firstName%', $firstName, $this->quote);
		$this->quote = str_replace('%lastName%', $lastName, $this->quote);
	}

	/**
	 *	Returns a JSON representation of this object.
	 *	
	 *	@return	String
	 */
	public function toJSON($escaper) {
		$json = '{ "id": ' . $this->getId() . ', "joke": "' . $escaper->escape($this->getQuote()) . '", "categories": [';
		$first = true;
		foreach($this->getCategories() as $c) {
			if(!$first) {
				$json .= ', ';
			}
			$json .= "\"" . $c->getName() . "\"";
			$first = false;
		}
		$json .= '] }';
		return $json;
	}

  /**
   *  Returns whether the given Quote equals this quote.
   *  Comparison based on id.
   *
   *  @param  $other  Quote instance
   *  @return boolean
   */
  public function equals($other) {
    return $this->getId() == $other->getId();
  }
}
