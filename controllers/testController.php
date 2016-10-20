<?php

//namespace flowmanage\controllers;


require 'lhRestfullInterface.php';
require __DIR__ .'/../include/db.php';
require __DIR__ .'/../model/test.php';
require __DIR__ .'/../include/JsonUtils.php';

// spl_autoload_register(function ($class_name) {
// 	 include $class_name . '.php';
// });


class testController implements lhRestfullInterface
{

	/*
	 * 
	 * get:/resources   get all  (with search arguments)
	 * post:/resources  add one resources
	 * delete:/resources delete all
	 * put:/resources  update many (usually no exist)
	 * 
	 * get:/resources/id get one
	 * post:/resources/id (no exist)
	 * put:/resources/id  (update this one)
	 * delete:/resources/id  (delete one)
	 * 
	 * 
	 * */		
	
	public static function get_resources($apiPath,$requestVars,$postBody){
		
		$dbConnection = DbAction::startTransaction();
		
		$result = DbAction::SelectByPage($dbConnection, 'test', $columnList, $orderby, 0, 0);
		
		$dbConnection = DbAction::commitTransaction($dbConnection);
		
		//var_dump($result);
		
		echo '{"data":'.json_encode($result).',"message":"","status":0}';
		
	}
	
	public static function post_resources($apiPath,$requestVars,$postBody){
		
		// $postBody conver to object
		
		// convert json to object
		
		$postObject = json_decode($postBody);
		
		//var_dump($postObject);
		
		$postObject = objectToObject($postObject,"test");
		
		
		//var_dump($postObject);

		
		$dbConnection = DbAction::startTransaction();
		
// 		$testdata = new test();
		
// 		$testdata->data = "new first";
		
// 		$testdata->version = 0;
		
		
		
		DbAction::insert($dbConnection, $postObject);
		
		$dbConnection = DbAction::commitTransaction($dbConnection);
		
		echo '{"data":"","message":'.json_encode($postObject).',"status":0}';
		
	}
	
	public static function delete_resources($apiPath,$requestVars,$postBody){
		
		
	}
	
	
	public static function put_resources($apiPath,$requestVars,$postBody){
		
		
	}
	
	
	public static function get_resource_by_id($apiPath,$requestVars,$postBody){
		
		
	}
	public static function post_resource_by_id($apiPath,$requestVars,$postBody){
		
		// 
		
	}
	public static function delete_resource_by_id($apiPath,$requestVars,$postBody){
		
		// $postBody conver to object
		
		$dbConnection = DbAction::startTransaction();
		
		$testdata = new flowmanagement\model\test();
		
		$testdata->data = "new first";
		
		$testdata->version = 0;
		
		DbAction::insert($dbConnection, $testdata);
		
		$dbConnection = DbAction::commitTransaction($dbConnection);
		
		echo '{"data":"","message":"","status":0}';
	}
	public static function put_resource_by_id($apiPath,$requestVars,$postBody){
		
		// $postBody conver to object
		
		$dbConnection = DbAction::startTransaction();
		
		$testdata = new flowmanagement\model\test();
		
		$testdata->data = "modify";
		
		$testdata->version = 0;
		
		$testdata->id = 1;
		
		DbAction::save($dbConnection, $testdata);
		
		$dbConnection = DbAction::commitTransaction($dbConnection);
		
		echo '{"data":"","message":"","status":0}';
	}
	
	

}