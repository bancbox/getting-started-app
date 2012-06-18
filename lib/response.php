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
 * Response interface.
 * 
 * @package FinallyPHP.error
 */
interface IResponse
{
	// protected methods inherited from Exception class
	
	public function getMessage();
	public function getCode();
	public function getFile();
	public function getLine();
	public function getTrace();
	public function getTraceAsString();
	
	// overrideable methods
	
	public function __construct($data, $data = '');
	public function __toString();
	
	// new methods
	
	public function log();
	public function output();
}

/**
 * Extended exception class.
 * 
 * @package FinallyPHP.error
 */
class Response extends Exception implements IResponse
{
	/**
	 * Response code.
	 *
	 * @var int
	 */
	protected $code = 0;
	
	/**
	 * Response data.
	 *
	 * @var array
	 */
	protected $data;
	
	/**
	 * Response constructor.
	 *
	 * @param          int $code    Response code
	 * @param string|array $message Response data
	 */
	public function __construct($code, $data = '')
	{
		if(!is_array($data))
		{
			$data = array('message' => $data);
		}
		if (!isset($data['message']))
		{
			$data['message'] = '';
		}
		
		$this->data = $data;
		$this->data = array_merge(array('code' => $code), $this->data);
		
		parent::__construct($data['message'], $code);
	}
	
	/**
	 * String casting.
	 *
	 * @return string Response output
	 */
	public function __toString()
	{
		return json_encode($this->data);
	}
	
	/**
	 * Response log.
	 * 
	 */
	public function log()
	{
		log\add('response', $this->data);
	}
	
	/**
	 * Response output display.
	 */
	public function output()
	{
		try
		{
			core\load_response(array(
				'code' => $this->data['code'],
				'data' => $this->data
			));
		}
		catch(Error $e)
		{
			$e->log();
			$e->output();
		}
	}
}
