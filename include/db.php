<?php


class DbAction {
	
	private static $databaseName = "restful_framework";
	private static $dbUser = "root";
	private static $dbUserPassword = "root";
	private static $databaseServerName = "localhost";
	
	public static function uniqueFindById($dbConnection,$Object, $value) {
		
		$className = self::getClassName(get_class ( $Object ));
		
	   if (strcmp ( $className, "" ) == 0) {
			return false;
		}
		
		$queryString = "select * from " . $className . " where id = ".$value;
				
		$result = $dbConnection->query ( $queryString );
		
		// get class vars and results vars 
		
		// if class property name === results vars name , then set.
		
		// after process all result , then return the object
		
		if($result == false)
		{
			self::rollbackTransaction($dbConnection);
		}
		
		return $result;
		
	}
	
	
	
	public static function save($dbConnection,$Object, $whereName) {
		
		// Argu1: $Object is the object which we want update in the persistent data.
		// Argu2: $whereName is the condition when update the object (in sql it’s "where wherename=value"). If the whereName equal "", default the whereName will be the Id.
		// If the condition does not specified the unique rcord, will update all the record all matched the condition. //Return: true or false
		// step1: get the class name (now didn’t support bunch update )
		// step2: get the class all properties and values
		// setp3: create the update sql and update
		
		$className = self::getClassName(get_class ( $Object ));
		
		if (strcmp ( $className, "" ) == 0) {
			return false;
		}
		if ($whereName == "")
			$whereName = "id";
		$whereValue = -255;
		$class_vars = get_class_vars ( get_class ( $Object ) );
		$queryString = "update " . $className . " set ";
		
		// optimistic lock
		$whereVersion = $Object->version;
		if(empty($whereVersion)) {
			
			$optimisticlock = "version = ".$Object->version;
		}
		
		foreach ( $class_vars as $name => $value ) {  
			if ($name == $whereName)
				$whereValue = $Object->$name;
			else {
				if ($Object->$name != "" && strcasecmp($name,"id") != 0 && strcasecmp($name,"version") != 0)
					$queryString = $queryString . $name . "='" . $Object->$name . "' ,";
				
				else if (strcasecmp($name,"version") == 0)
					
					$queryString = $queryString . $name . "='" . ($Object->$name + 1). "' ,";
				
			}
		}
		
		if ((strcasecmp($whereName,"Id") == 0 && $whereValue == "") || $whereValue == - 255)
			return false;
		
		$queryString = trim ( $queryString, "," ) . " where " . $whereName . "='" . $whereValue . "'";
		
		if(empty($optimisticlock) )  $queryString = $queryString." and ".$optimisticlock;
		
		//echo $queryString;
		
		$result = $dbConnection->query ( $queryString );
		
		if($result == false)
		{
			self::rollbackTransaction($dbConnection);
		}
		
		return $result;
	}
	
	public static function Select($dbConnection,$tableName, $columnList, $orderby, $limit) {
		
		// return error or "" or ArrayList (if no result selected will return "" , if error occured will return false )
		// if(strcmp(get_class($Object),"") == 0)
		// {
		// return false;
		// }
		
		//$className = self::getClassName(get_class ( $Object ));
		
		$queryString = "SELECT ";
		if ($columnList != "") {
			if (is_array ( $columnList )) {
				foreach ( $columnList as $column ) {
					$queryString .= $column . ",";
				}
			} else {
				$queryString .= $columnList;
			}
		} else
			$queryString .= "*";
		$queryString = trim ( $queryString, "," ) . " FROM " . $tableName;
		if ($orderby != "")
			$queryString .= " order by " . $orderby;
		if ($limit != "")
			$queryString .= " limit " . $limit;
		
		//echo $queryString;
		$result = $dbConnection->query ($queryString );
		$resultData = array();
		if ($result) {
			$i = 0;
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$resultData [$i] = $row;
				$i ++;
			}
		}
		return $resultData;
	}
	
	

	public static function SelectByPage($dbConnection,$tableName, $columnList, $orderby, $pageSize , $pageNumber) {
	
		// return error or "" or ArrayList (if no result selected will return "" , if error occured will return false )
		// if(strcmp(get_class($Object),"") == 0)
		// {
			// return false;
			// }
	
		//	$className = self::getClassName(get_class ( $Object ));
		
		if($pageSize <= 0 ) $pageSize = 20;
		if($pageNumber <= 1 ) $pageNumber = 1;
		
		$start = $pageSize * ($pageNumber-1)  ; // limit start 0
		$end = $pageSize * $pageNumber;
	
		$limit = $start.",".$end;
		return self::Select($dbConnection, $tableName, $columnList, $orderby, $limit);
		
	}
	
	public static function delete($dbConnection,$Object, $whereName) {
		
		$className = self::getClassName(get_class ( $Object ));
		
		if (strcmp ( $className, "" ) == 0) {
			return false;
		}
		
		if ($whereName == "")
			$whereName = "id";
		$whereValue = - 255;
		$class_vars = get_class_vars ( get_class ( $Object ) );
		$queryString = "delete from " . $className;
		foreach ( $class_vars as $name => $value ) {
			if ($name == $whereName) {
				$whereValue = $Object->$name;
				break;
			}
		}
		if (($whereName == "id" && $whereValue == "") || $whereValue == - 255)
			return false;
		$queryString .= " where " . $whereName . "='" . $whereValue . "'";
		//echo $queryString;
		$result = $dbConnection->query ($dbConnection, $queryString );
		
		if($result == false)
		{
			self::rollbackTransaction($dbConnection);
		}
		
		return $result;
	}
	
	
	public static function insert($dbConnection,$Object) {
		
		$className = self::getClassName(get_class ( $Object ));
		
		$class_vars = "";
		$queryString = "";
		$insertValues = "";
		
		$Object->version = 0;
		
		if (is_array ( $Object )) {
			// Class object array , could bunch insert data
		} else {
			if (strcmp ( $className, "" ) == 0) {
				return false;
			}
			$class_vars = get_class_vars ( get_class ( $Object ) );
			$queryString = "insert into " . $className . " (";
		}
		
		foreach ( $class_vars as $name => $value ) {
			if ($name != "id" && isset($Object->$name)) {
				$queryString .= $name . ",";

// 				var_dump($name);
// 				var_dump($Object->$name);
// 				var_dump($Object->$name === "NULL");
				
				$insertValues .= "'".(($Object->$name === "NULL") ?"NULL":$Object->$name)."',";
			}
		}
		//echo $insertValues . "";
		$queryString = trim ( $queryString, "," ) . ") values (" . trim ( $insertValues, "," ) . ")";
		//echo $queryString . "";

		$result = $dbConnection->query ( $queryString );

		if($result == false)
		{
			self::rollbackTransaction($dbConnection);
			die('{"message":"roll back!"}');
		}
		
		return $result;
	}
	
	
	public static function deleteAll($dbConnection,$tableName)
	{		
		$queryString = "delete from $tableName";
		//echo $queryString . "";
		
		$result = $dbConnection->query ( $queryString );
		
		if($result == false)
		{
			self::rollbackTransaction($dbConnection);
		}
		
		return $result;
		
	}
	
	
	private static function getClassName($className)
	{
		
		$lastClassName = explode("\\", $className);
		return $lastClassName[count($lastClassName)-1];
		
	}
	

	
	public static function startTransaction(){
		
		$dbConnection = self::_connectDb ();
		
		$dbConnection->autocommit(FALSE);
		
		$dbConnection->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		return $dbConnection;
		
	}
	
	public static function commitTransaction($dbConnection){
		
			if (!$dbConnection->commit()) {
		    print("Transaction commit failed\n");
		    exit();
		}
		
	}
	
	
	public static function rollbackTransaction($dbConnection){
		
		if (!$dbConnection->rollback()) {
			print("Transaction rollback failed\n");
			exit();
			
			// throw excepttion
			
		}
	}
	
	
	public static function _connectDb() {
		
	
	//	$_db_con = mysql_connect ( self::$databaseServerName, self::$dbUser, self::$dbUserPassword );
	
		if(!class_exists('mysqli')) die('{"message":"mysqli is not exist!"}');

		$_db_con = new mysqli(self::$databaseServerName, self::$dbUser, self::$dbUserPassword, self::$databaseName);


		if($_db_con->connect_errno > 0){
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
   /*
		if (! $_db_con)
			die ( 'Could not connect: ' . mysql_error () );
		else
			mysql_select_db ( self::$databaseName, $_db_con );
	*/
		return $_db_con;
	}
	
	public static function _disconnectDb($_db_con) {
		//mysql_close ( $_db_con );
		$_db_con->close();
		
	}
	
}

// $db = new DbAction();
// $db->testDb();

?>