<?php


require 'lhRestfullInterface.php';
require '../model/test.php';


class usersController implements lhRestfullInterface
{


	/*
	 * 
	 * 
	 * get:/resources   get all  (with search arguments)
	 * post:/resources  add one resources
	 * delete:/resources delete all
	 * put:/resources  update all (usually no exist)
	 * 
	 * get:/resources/id get one
	 * post:/resources/id (no exist)
	 * put:/resources/id  (update this one)
	 * delete:/resources/id  (delete one)
	 * 
	 * 
	 * */		
	
	public static function get_resources($apiPath,$requestVars,$postBody){
		
		echo '{["a","b"]}';
		
	}
	public static function post_resources($apiPath,$requestVars,$postBody){
		
	}
	public static function delete_resources($apiPath,$requestVars,$postBody){
		
	}
	public static function put_resources($apiPath,$requestVars,$postBody){
		
	}
	
	
	public static function get_resource_by_id($apiPath,$requestVars,$postBody){
		
	}
	public static function post_resource_by_id($apiPath,$requestVars,$postBody){
		
	}
	public static function delete_resource_by_id($apiPath,$requestVars,$postBody){
		
	}
	public static function put_resource_by_id($apiPath,$requestVars,$postBody){
		
	}
	
	

}