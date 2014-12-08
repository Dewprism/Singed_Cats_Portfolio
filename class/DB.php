<?php


class DB {
	protected $db_name = '***';
	protected $db_user = '***';
	protected $db_pass = '***';
	protected $db_host = '***';
	public $numRows;
	
	//Open a connection to the database.
	public function connect() {
		$connection = mysql_connect($this->db_host, $this->db_user, $this->db_pass);
		mysql_select_db($this->db_name);
		return true;
	}
	
	//Turns the database result into an array
	public function dataToArray($data)
	{
		$resultArray = array();
		while($row = mysql_fetch_assoc($data)) {
			array_push($resultArray, $row);
		}
        
		//If it's only one row, don't return it as an array
		$this->numRows = mysql_num_rows($data);
		if($this->numRows == 1) {
			return $resultArray[0];
		}
		else return $resultArray;
	}
	
	//Select from the database without specific columns.
	public function selectAll($table, $where = "") {
		if ($where == "") $sql = "select * from $table;";
		else $sql = "select * from $table where $where;";

		$result = mysql_query($sql);
		if (!$result) {
			die($sql . ":\n" . mysql_error());	
		}
		
		return $this->dataToArray($result);
	}
	
	//Select from database given a list of columns.
	public function selectSome($cols, $table, $where = "", $order = "") {
		$sql = "select $cols from $table";

		if ($where != "") $sql = $sql . " where $where";
		if ($order != "") $sql = $sql . " order by $order";
		$sql .= ";";
		
		$result = mysql_query($sql);
		if (!$result) {
			die($sql . ":\n" . mysql_error());	
		}
		
		return $this->dataToArray($result);
	}
    
    public function selectCount($col, $table, $where = "") {
        $sql = "select count($col) as count from $table";
        if ($where != "") $sql .= " where $where";
        $sql .= ";";
        
        $result = mysql_query($sql);
        if (!$result) {
			die($sql . ":\n" . mysql_error());	
		}
        $resultArray = $this->dataToArray($result);
        return $resultArray["count"];
    }
	
	//Updates given row in database.
	public function update($data, $table, $where) {
		foreach ($data as $column => $value) {
			$sql = "update $table set $column = $value where $where;";
			mysql_query($sql) or die(mysql_error() . ":\n\n" . $sql);
		}
		return true;
	}
	
	//Inserts row into database.
	public function insert($data, $table) {
		$columns = "";
		$values = "";
		
		foreach ($data as $column => $value) {
			$columns .= ($columns == "")? "" : ", "; //Put a comma before subsequent entries
			$columns .= $column;
			$values .= ($values == "")? "" : ", ";
			$values .= $value;
		}
		
		$sql = "insert into $table ($columns) values ($values);";
		mysql_query($sql) or die(mysql_error() . ":\n\n" . $sql);
		return mysql_insert_id();
	}
	
	//Deletes row from database.
    public function delete($table, $where) {
		$sql = "delete from $table where $where;";
		mysql_query($sql) or die(mysql_error());
		return true;
	}
	
	//Checks db for username and password match, and sets session variables if found
	public function login($username, $password) {
		$result = mysql_query("select * from users where username = '$username' and password = '$password';");
        
		if (mysql_num_rows($result) == 1) {
			$loggedUser = new User(mysql_fetch_assoc($result));
			$_SESSION["singedcats_username"] = $username;
			$_SESSION["singedcats_userID"] = $loggedUser->id;
            $_SESSION["singedcats_fname"] = $loggedUser->fname;
			$_SESSION["singedcats_loginTime"] = time();
			$_SESSION["singedcats_loggedIn"] = 1;
			return(true);
		}
		else{
			return false;
		}
	}
	
}
?>