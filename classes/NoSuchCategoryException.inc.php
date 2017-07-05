<?php
/**
 *	Class used for representing the fact that a category was asked that doesn't exist in the database,
 *	most likely because of a non-existing id.
 */
class NoSuchCategoryException extends Exception {

	public function toJSON() {
		return '{ "message": "' . $this->message . '" }';
	}

}
