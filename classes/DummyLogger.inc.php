<?php
/**
 *	Class used for inserting a dummy logging object when needed.
 *	A dummy logger implements the Logger interface but doesn't do anything.
 */
class DummyLogger {

	public function log($m) {}

	public function error($e) {}

	public function warning($w) {}

	public function eLog($location, $m) {}

	public function eError($location, $e) {}

	public function eWarning($location, $w) {}
	
}
