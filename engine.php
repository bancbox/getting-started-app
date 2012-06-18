<?php

/*!
 * FinallyPHP
 * 
 * @link http://sourceforge.net/projects/finallyphp/
 * @author Ovidiu Chereches <hello@ovidiu.ch>
 * 
 * @copyright Copyright (c) 2010, Ovidiu Chereches
 * @license http://legal.ovidiu.ch/licenses/MIT MIT License
 */

define('HOST_NAME', $_SERVER['SERVER_NAME']);

/* Base paths */

$project_path = __DIR__;
$app_path     = "$project_path/app";
$config_path  = "$project_path/conf";
$lib_path     = "$project_path/lib";
$log_path     = "$project_path/log";
$public_path  = "$project_path/public";

/* Core lib */

require_once("$lib_path/_core/core.functions.php");
file\require_more("$lib_path/_core");

/* Core config */

conf::set('PROJECT_PATH', $project_path);
conf::set('APP_PATH',     $app_path);
conf::set('CONFIG_PATH',  $config_path);
conf::set('LIB_PATH',     $lib_path);
conf::set('LOG_PATH',     $log_path);
conf::set('PUBLIC_PATH',  $public_path);

conf::set('MODEL_PATH',        '{APP_PATH}/models');
conf::set('CONTROLLER_PATH',   '{APP_PATH}/controllers');
conf::set('VIEW_PATH',         '{APP_PATH}/views');
conf::set('VIEW_PAGE_PATH',    '{VIEW_PATH}/pages');
conf::set('VIEW_LAYOUT_PATH',  '{VIEW_PATH}/layouts');
conf::set('VIEW_PARTIAL_PATH', '{VIEW_PATH}/partials');
conf::set('VIEW_ERROR_PATH',   '{VIEW_PATH}/errors');
conf::set('HELPER_PATH',       '{APP_PATH}/helpers');

conf::set('APP_LIB_PATH',    '{LIB_PATH}/{APP}');
conf::set('APP_CONFIG_PATH', '{CONFIG_PATH}/{APP}');

conf::set('APP_MODEL_PATH',        '{MODEL_PATH}/{APP}');
conf::set('APP_CONTROLLER_PATH',   '{CONTROLLER_PATH}/{APP}');
conf::set('APP_VIEW_PATH',         '{VIEW_PATH}/{APP}');
conf::set('APP_VIEW_PAGE_PATH',    '{VIEW_PAGE_PATH}/{APP}');
conf::set('APP_VIEW_LAYOUT_PATH',  '{VIEW_LAYOUT_PATH}/{APP}');
conf::set('APP_VIEW_PARTIAL_PATH', '{VIEW_PARTIAL_PATH}/{APP}');
conf::set('APP_VIEW_ERROR_PATH',   '{VIEW_ERROR_PATH}/{APP}');
conf::set('APP_HELPER_PATH',       '{HELPER_PATH}/{APP}');

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
{
	conf::set('PROTOCOL', 'https');
}
else
{
	conf::set('PROTOCOL', 'http');
}

conf::set('URL', conf::get('PROTOCOL') . '://' . HOST_NAME . preg_replace
(
	'#(/public)?/proxy.php#', '', $_SERVER['SCRIPT_NAME']
));

/* Config */

conf::set('APPLICATION_ENV', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'unknown');
file\require_more($config_path);

/* Lib */

file\require_more($lib_path);
//file\require_more("$lib_path/external");

/* App, controller, action and extension */

conf::set('APP',        conf::get('DEFAULT_APP'));
conf::set('CONTROLLER', conf::get('DEFAULT_CONTROLLER'));
conf::set('ACTION',     conf::get('DEFAULT_ACTION'));
conf::set('EXTENSION',  conf::get('DEFAULT_EXTENSION'));

$query_pattern = '/^[a-z0-9-_]*$/i';

if(!isset($_GET['extension'])  || !preg_match($query_pattern, $_GET['extension'])  ||
   !isset($_GET['app'])        || !preg_match($query_pattern, $_GET['app'])        ||
   !isset($_GET['controller']) || !preg_match($query_pattern, $_GET['controller']) ||
   !isset($_GET['action'])     || !preg_match($query_pattern, $_GET['action']))
{
	throw new Error(404, 'Invalid URL request');
}

/* Model init */

model\names::index(conf::get('MODEL_PATH'));

/* Page load */

core\load_page($_GET['app'], $_GET['controller'], $_GET['action'], $_GET['extension']);
