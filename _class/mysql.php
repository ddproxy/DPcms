<?php

class mysql {
	private $host;
	private $username;
	private $password;
	private $schema;
	private $SQL;
	private $config;
	public $query;
	public $count;

	public function __construct() {
		$this->config = parse_ini_file('./settings/config.ini');
		$this->host = $this->config['host'];
		$this->password = $this->config['password'];
		$this->username = $this->config['username'];
		$this->schema = $this->config['schema'];
		$this->connect();
	}
	public function connect() {
	$this->SQL = mysql_connect($this->host,$this->username,$this->password) or die("Could not connect. " . mysql_error() );
	mysql_select_db($this->schema, $this->SQL) or die("Could not select database. " . mysql_error());
	return $this->buildDB();	
	}
	public function query($query, $return='true') {
		$this->query = $query;
		$this->count++;
		$result = mysql_query($query) or die('Error with query('.$query.'):'.mysql_error());
		if ($return)
			return $result;
	}

	// MySQL functions

	public function num_rows(&$result) {
		return @mysql_num_rows($result);
	}
	public function fetch_array(&$result) {
		return @mysql_fetch_array($result);
	}
	public function fetch_assoc(&$result) {
		return @mysql_fetch_assoc($result);
	}
	public function insert_id(&$result) {
		return @mysql_insert_id($result);
	}
	public function disconnect() {
		mysql_close($this->SQL);
	}
	public function escape(&$string) {
		return mysql_real_escape_string($string);
	}
	public function result($query, $column, $id=0) {
		return mysql_result($query, $id, $column);
	}

	private function buildDB() {
	
	$sql = <<<MySQL_QUERY
	CREATE TABLE IF NOT EXISTS content (
		title		VARCHAR(150),
		bodytext 	TEXT,
		created	VARCHAR(100)
	)
MySQL_QUERY;
	$this->query($sql);
	$sql = <<<MySQL_QUERY
	CREATE TABLE IF NOT EXISTS users (
		id		INT(150) NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id),
		username 	VARCHAR(128) NOT NULL,
		password	VARCHAR(32) NOT NULL,
		date_added	TIMESTAMP,
		last_login	TIMESTAMP,
		access_level	INT
	)
MySQL_QUERY;
	
	return $this->query($sql);

	}
}

?>