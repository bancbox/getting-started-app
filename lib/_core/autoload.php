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

/**
 * PHP class autoloader. Used for models and controllers.
 *
 * @package FinallyPHP.core
 *
 * @param string $classname Class name
 */
function autoload_finallyphp($classname)
{
	$filename = str\filename_from_camelcase($classname);
	$places = array();
	
	if(class_exists('conf') && conf::is_defined('PROJECT_PATH'))
	{
		$places[] = conf::get('MODEL_PATH');
		$places[] = conf::get('CONTROLLER_PATH');
		
		if(conf::is_defined('APP'))
		{
			$places[] = conf::get('APP_MODEL_PATH');
			$places[] = conf::get('APP_CONTROLLER_PATH');
		}
	}
	
	foreach($places as $place)
	{
		$filepath = "$place/$filename.php";
		if(file_exists($filepath))
		{
			require_once($filepath);
		}
	}
}
spl_autoload_register('autoload_finallyphp');