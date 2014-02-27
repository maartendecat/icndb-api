<?php
/**
 *	Class used for representing a database. 
 */
class Database {

	/**
	 *	Constructs this new database with given parameters and connects to the database.
	 *	
	 *	Note: To connect on a different port than 3307, use $dbhost="host:port".
	 *
	 *	@throws DatabaseConnectionException
	 *			If connecting to the database with given arguments failed.
	 */
	public function __construct($dbhost, $dbname, $dbuser, $dbpass) {
		$this->connect($dbhost, $dbname, $dbuser, $dbpass);
	}

	/**
	 *	Close the db connection at the end of the script.
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 *	The connection to the db.
	 */
	private $connection;

	/**
	 *	Whether this object is connected or not.
	 */
	private $connected;
	public function isConnected() {
		return $connected;
	}

	/**
	 *	Connects to the database.
	 *
	 *	@throws DatabaseConnectionException
	 *			If connecting to the database with given arguments failed.
	 */
	private function connect($dbhost, $dbname, $dbuser, $dbpass) {
		$this->connection = mysql_connect($dbhost, $dbuser, $dbpass);
//		if($this->connection === FALSE) {
//			throw new DatabaseConnectionException();
//		}
		mysql_select_db($dbname);
		$connected = true;
	}

	/**
	 *	Closes the database connection. Should be called at the end of 
	 *	every script that opened a connection.
	 */
	public function close() {
		mysql_close($this->connection);
	}

	/**
	 * Executes the given query and returns the raw result.
	 */ 
	public function query($query) {
		return mysql_query($query);
	}
}
