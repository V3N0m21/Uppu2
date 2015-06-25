<?php namespace Classes\Model;
use ORM;
class Connection extends ORM {
	public function __construct() {
		$this->configure('mysql:host=localhost;dbname=publications;charset=utf8');
		$this->configure('username', 'user');
		$this->configure('password', '1234567');
		
	}
}