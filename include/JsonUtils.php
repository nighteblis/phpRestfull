<?php

function objectToJson($obj) {
	
	return json_encode($obj);
}


function jsonToObject($json) {

	return json_decode($json);
}

function objectToObject($instance, $className) {
	return unserialize(sprintf(
			'O:%d:"%s"%s',
			strlen($className),
			$className,
			strstr(strstr(serialize($instance), '"'), ':')
			));
}


?>