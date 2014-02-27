<?php
/**
 *	Class used for representing the fact that a quote was asked that doesn't exist in the database,
 *	most likely because of a non-existing id.
 */
class NoSuchQuoteException extends Exception {

	public function toJSON() {
		return '{ "message": "' . $this->message . '" }';
	}

}
