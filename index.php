<?php
define('WEBROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']));

require(ROOT . 'core/model.php');
require(ROOT . 'core/controller.php');

$params = explode('/', $_GET['url']);
$controller = !empty($params[0]) ? $params[0] : 'default';
$action = isset($params[1]) && !empty($params[1]) ? $params[1] : 'index';

$controller = strtolower($controller);
$action = strtolower($action);

if(!file_exists('controllers/' . $controller . '.controller.php')){
    require('404.php');
}
else {
    require('controllers/' . $controller . '.controller.php');
    $controller = ucfirst($controller) . 'Controller';
    $controller = new $controller();
    if (!method_exists($controller, $action)){
        require('404.php');
    }
    else {
        unset($params[0]);
        unset($params[1]);
        call_user_func_array(array($controller, $action), $params);
    }
}


//
//header('Content-Type: application/json');
//
//define('WEBROOT', str_replace('index.php','',
//    "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']));
//define('ROOT', str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));
//
//require(ROOT . 'core/model.php');
//require(ROOT . 'core/controller.php');
//
//$params = explode('/', $_GET['url']);
//unset($_GET['url']);
//
//if(!isset($params[0]) || empty($params[0]))
//    jsonQuit("Error : No matching routes.");
//$ctrl = $params[0];
//$action = "all";
//if(isset($params[1]) && !empty($params[1]))
//    $action = $params[1];
//
//if(file_exists('controllers/'. $ctrl .'_controller.php')) {
//    require('controllers/' . $ctrl . '_controller.php');
//    $ctrl = new $ctrl();
//
//    if (method_exists($ctrl, $action))
//        call_user_func_array(array($ctrl, $action), array_slice($params, 2));
//    else
//        jsonQuit("Error : Method $action not found");
//}
//else
//    jsonQuit("Error : Controller $ctrl not found.");
//
//function jsonQuit($msg) {
//    die(json_encode(["error" => $msg]));
//}