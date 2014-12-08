<?php
require_once 'DB.php';

class User {
	public $id;
	public $username;
	public $password;
	public $fname;
	public $lname;
	public $email;

	//Constructor takes an associative array.
	function __construct($data) {
		$this->id = (isset($data['id']))? $data['id'] : "";
		$this->username = (isset($data['username']))? $data['username'] : "";
		$this->password = (isset($data['password']))? $data['password'] : "";
		$this->fname = (isset($data['fname']))? $data['fname'] : "";
		$this->lname = (isset($data['lname']))? $data['lname'] : "";
		$this->email = (isset($data['email']))? $data['email'] : "";
	}

	public function register() {
		$db = new DB();
		
		$data = array(
			"username" => "'$this->username'",
			"password" => "'$this->password'",
			"fname" => "'$this->fname'",
			"lname" => "'$this->lname'",
			"email" => "'$this->email'",
		);
		
		$result = mysql_query("select id from users where username = '$this->username';");
    	if(mysql_num_rows($result) == 0) {
			$this->id = $db->insert($data, 'users');
			return true;
	   	}
		else {
	   		return false;
		}
	}
	
}
?>