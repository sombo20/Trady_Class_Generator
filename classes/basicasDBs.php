<?php

class basicasDBs {

	private $database;
	
	public function __construct($database) {
		$this->database = $database;
	}
	
	public function getDatabase() {
		return $this->database;
	}

}
?>