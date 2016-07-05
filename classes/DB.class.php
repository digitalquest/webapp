<?php
//DB.class.php

class DB {

	protected $db_name = 'homestead';
	protected $db_user = 'homestead';
	protected $db_pass = 'secret';
	protected $db_host = 'localhost';
/*	
	//constructor
	public function __construct($db_name, $db_user, $db_pass, $db_host='localhost') {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;
	}
*/
	//open a connection to the database. Make sure this is called
	//on every page that needs to use the database.
	public function connect() {
		$connection = mysql_connect($this->db_host, $this->db_user, $this->db_pass) or die(mysql_error());
		mysql_select_db($this->db_name) or die(mysql_error());

		return true;
	}

	//takes a mysql row set and returns an associative array, where the keys
	//in the array are the column names in the row set. If singleRow is set to
	//true, then it will return a single row instead of an array of rows.
	public function processRowSet($rowSet, $singleRow=false)
	{
		$resultArray = array();
		while($row = mysql_fetch_assoc($rowSet))
		{
			array_push($resultArray, $row);
		}

		if($singleRow === true)
			return $resultArray[0];

		return $resultArray;
	}

	//Select rows from the database.
	//returns a full row or rows from $table using $where as the where clause.
	//return value is an associative array with column names as keys.
	public function select($table, $where) {
		$sql = "SELECT * FROM $table WHERE $where";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 1)
			return $this->processRowSet($result, true);

		return $this->processRowSet($result);
	}

	//Updates a current row in the database.
	//takes an array of data, where the keys in the array are the column names
	//and the values are the data that will be inserted into those columns.
	//$table is the name of the table and $where is the sql where clause.
	public function update($data, $table, $where) {
		foreach ($data as $column => $value) {
			$sql = "UPDATE $table SET $column = $value WHERE $where";
			mysql_query($sql) or die(mysql_error());
		}
		return true;
	}

	//Inserts a new row into the database.
	//takes an array of data, where the keys in the array are the column names
	//and the values are the data that will be inserted into those columns.
	//$table is the name of the table.
	public function insert($data, $table) {

		$columns = "";
		$values = "";

		foreach ($data as $column => $value) {
			$columns .= ($columns == "") ? "" : ", ";
			$columns .= $column;
			$values .= ($values == "") ? "" : ", ";
			$values .= $value;
		}

		$sql = "insert into $table ($columns) values ($values)";

		mysql_query($sql) or die(mysql_error());

		//return the ID of the user in the database.
		return mysql_insert_id();

	}
	
	//delete row in the database given a where clause
	//if no where clause is specifies, defaults to delete *
	public function delete($table, $where='*') {
        switch ($where) { 
            case "":
                $sql ="DELETE FROM $table";
                break;
            case "*":
                $sql ="DELETE FROM $table";
                break;
            default:
                $sql ="DELETE FROM $table WHERE $where";
        }
        
        mysql_query($sql) or die(mysql_error());
        
        return mysql_affected_rows();
	}

}

?>
