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

namespace core;


function load_response($data = null)
{
	$extension = \conf::get('EXTENSION');
	
	$view_path = \view\get_page_path('response', 'main', $extension);
	\controller\load('response', 'main', $extension, $data, $view_path);
}