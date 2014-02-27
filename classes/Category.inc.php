<?php
/**
 *	Class used for representing a category in the system.
 *	
 *	A CATEGORY INSTANCE IS IMMUTABLE
 */
class Category {
	
	// NO SETTER! Categories are frozen in the system.
	private $id;
	public function getId() {
		return $this->id;
	}

	// NO SETTER! Categories are frozen in the system.
	private $name;
	public function getName() {
		return $this->name;
	}

	// NO SETTER! Categories are frozen in the system.
	private $description;
	public function getDescription() {
		return $this->description;
	}

	/**
	 *	Initialize this new category with given id, name and description.
	 */
	public function __construct($id, $name, $description) {
		$this->id = $id;
		$this->name = $name;
    $this->description = $description;
	}

	/**
	 * 	Returns whether this category equals the given category.
	 *	Two categories equal when having the same id.
	 *	
	 *	@param	$other	Category instance
	 *	@return	Boolean
	 */
	public function equals($other) {
		return $this->getId() == $other->getId();
	}

	/**
	 *	Returns a JSON representation of this object.
	 *
	 *	@return	String
	 */
	public function toJSON() {
		return "NOT YET IMPLEMENTED";
	}
}
