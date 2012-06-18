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
 * Notice class.
 * 
 * @package FinallyPHP
 */
class Notice
{
	public function __construct($code, $data = '', $url = null)
	{
		if(!is_array($data))
		{
			$data = array('message' => $data);
		}
		
		if ($url)
		{
			$data = array_merge($data, array('target' => $url));
		}
		
		$response = new Response($code, $data);
		
		if(conf::get('EXTENSION') != conf::get('DEFAULT_EXTENSION'))
		{
			throw $response;
		}
		//$response->log();
		
		// push notice
		
		if($url)
		{
			url\redirect($url);
		}
	}
}
