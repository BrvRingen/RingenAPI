<?php
//ORG: https://github.com/spoehner/rest-api-example
// simple autoloader
spl_autoload_register(function ($className) {
	if (substr($className, 0, 4) !== 'Api\\') {
		// not our business
		return;
	}

	$fileName = __DIR__.'/'.str_replace('\\', DIRECTORY_SEPARATOR, substr($className, 4)).'.php';

	if (file_exists($fileName)) {
		include $fileName;
	}
	//if(strpos($fileName, 'CsController') !== false)
	//	throw new Exception("Search for file: ".$fileName);
});

// get the requested url
//$url      = (isset($_GET['_url']) ? $_GET['_url'] : '');
$url = $_SERVER['REQUEST_URI'];
$urlParts = array_reverse(explode('/', $url));

// shift the version away
array_shift($urlParts);

// build the controller class
$controllerName      = (isset($urlParts[0]) && $urlParts[0] ? $urlParts[0] : 'index');
$controllerClassName = '\\Api\\Controller\\'.ucfirst($controllerName).'Controller';

// build the action method
$actionName       = 'index'; //(isset($urlParts[1]) && $urlParts[1] ? $urlParts[1] : 'index');
$actionMethodName = $actionName.'Action';

$controller = new $controllerClassName();
$controller->$actionMethodName();