<?php
$requesturi = $_SERVER ['REQUEST_URI'];
$requestmethod = $_SERVER ['REQUEST_METHOD'];
$requestData = array (); // 请求的封装数据， 传递给对应的controller

if($requestmethod == "POST" || $requestmethod == "PUT")
	$postdata = file_get_contents("php://input");

header ( 'Content-Type: application/json' ); // 默认返回json类型内容

$authorized = true; // 后续实现

/*
 * http request uri example : get method , http://localhost/index.do?api=/users/&pageSize=5&pageNumber=1 method=get api=/users query parameters: pageSize=5 , pageNumber=1
 */

$apiPatternMatched = false;
$subject = $requesturi;
$pattern = '/api\..+\?(.+)/';
$matchresult = preg_match ( $pattern, $subject, $matches, PREG_OFFSET_CAPTURE );

if($matchresult == 0)
{
	die('{"data":"","message":"server unknown error!api uri match error!","status":500}');	
}
$httpRequestArgumentsArray = explode ( "&", $matches [1] [0] );

// echo "<br/>";

foreach ( $httpRequestArgumentsArray as $key => $value ) {
	// echo "{$key} => {$value} ";
	$tmpkeyvalue = explode ( "=", $value );
	if ($tmpkeyvalue [0] === "api") {
		$apiuri = $tmpkeyvalue [1];
		$apiPatternMatched = true;
		continue;
	}
	$requestNewArgumentsRemovedApi [$tmpkeyvalue [0]] = $tmpkeyvalue [1];
	// print_r($arr);
}

// get api first value ,execute the api

// print_r($restfullArguments);

// print("<br/>");

$apiuriArray = explode ( "/", $apiuri );
$controller = $apiuriArray[1] . "Controller";
$apiuriWithMethod = $requestmethod . ":" . $apiuri;

$requestData [0] = $apiuriWithMethod;

if (! empty ( $requestNewArgumentsRemovedApi )) // 如果为空，则不添加null，无意义
	$requestData [1] = $requestNewArgumentsRemovedApi;
	
if ($apiPatternMatched) {
	
	// print_r($requestData);
	// call the api
	// echo "<br>find";
	
	// authorization
	if (! $authorized) {
		die ( '{"data":"","message":"you are not permitted to access!","status":401}' );
	}
	
	try {
		
		$resourcesOperation = false;
		if(empty($apiuriArray[2]) || !is_integer($apiuriArray[2]) || $apiuriArray[2]<=0)
		{
			$resourcesOperation = true;
		}
		
		// echo 'controllers/'.$controller.'.php';
		require 'controllers/' . $controller . '.php';
		
		if($requestmethod == 'GET')
		{
			if($resourcesOperation)
				$returnData = $controller::get_resources ( $apiuriWithMethod, $requestData, $postdata );
			else 
				$returnData = $controller::get_resource_by_id ( $apiuriWithMethod, $requestData, $postdata );
		
		}
		else if($requestmethod == 'POST')
		{
			if($resourcesOperation)
				$returnData = $controller::post_resources ( $apiuriWithMethod, $requestData, $postdata );
			else
				$returnData = $controller::post_resource_by_id ( $apiuriWithMethod, $requestData, $postdata );
				
		}
		else if($requestmethod == 'PUT')
		{
			if($resourcesOperation)
				$returnData = $controller::put_resources ( $apiuriWithMethod, $requestData, $postdata );
			else
				$returnData = $controller::put_resource_by_id ( $apiuriWithMethod, $requestData, $postdata );
				
		}
		else if($requestmethod == 'DELETE')
		{
			if($resourcesOperation)
				$returnData = $controller::delete_resources ( $apiuriWithMethod, $requestData, $postdata );
			else
				$returnData = $controller::delete_resource_by_id ( $apiuriWithMethod, $requestData, $postdata );
				
		}		
		
		
	} catch ( RuntimeException $exception ) {
		die ( '{"data":"","message":"api not found!","status":404}' );
	} catch ( Exception $exception ) {
		
		die ( '{"data":"","message":"api not found!","status":404}' );
	}
	
	// will open lately
	//echo '{"data":"' . $returnData . '","request":' . json_encode ( $requestData, JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT ) . ',"message":"","status":0}';
} 

else {
	// api not found
	// redirect to the homepage
	die ( '{"data":"","message":"api not find!","status":404}' );
}