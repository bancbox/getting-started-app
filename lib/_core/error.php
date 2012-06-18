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
 * Error interface.
 * 
 * @package FinallyPHP.error
 */
interface IError
{
	// protected methods inherited from Exception class
	
	public function getMessage();
	public function getCode();
	public function getFile();
	public function getLine();
	public function getTrace();
	public function getTraceAsString();
	
	// overrideable methods
	
//	public function __construct($code, $message = '');
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
class Error extends Exception implements IError
{
	/**
	 * Error code.
	 *
	 * @var int
	 */
	protected $code = 0;
	/**
	 * Error message.
	 *
	 * @var string
	 */
	protected $message = '';
	/**
	 * Filename where error occurred.
	 *
	 * @var string
	 */
	protected $file;
	/**
	 * Line where error occurred.
	 *
	 * @var int
	 */
	protected $line;
	
	private $log;
	
	/**
	 * Error constructor.
	 *
	 * @param    int $code    Error code
	 * @param string $message Error message
	 */
	public function __construct($code, $message = '', $log = true)
	{
		parent::__construct($message, $code);
		
		$this->log = $log;
	}
	
	/**
	 * String casting.
	 *
	 * @return string Error message
	 */
	public function __toString()
	{
		return get_class($this) . ' ' . $this->code;
	}
	
	/**
	 * Error log.
	 * 
	 */
	public function log()
	{
		if ($this->log)
		{
			log\add('error', array(
				'code'    => $this->code,
				'message' => $this->message,
				'file'    => $this->file,
				'line'    => $this->line,
			));
		}
	}
	
	/**
	 * Error output display.
	 *
	 * Loads ErrorController actions based on the current environment.
	 * 
	 */
	public function output()
	{
		$environment = conf::get('ENVIRONMENT');
		try
		{
			core\load_error($environment, array(
				'code'    => $this->code,
				'message' => $this->message,
				'file'    => $this->file,
				'line'    => $this->line,
				'trace'   => $this->stacktrace($this->getTrace())
			));
		}
		catch(Error $e)
		{
			header('HTTP', true, $this->code);
			exit($e);
		}
	}
	
	/**
	 * Stacktrace helper.
	 *
	 * @param  array $collection Stacktrace data
	 * @return array             Modified stacktrace data
	 */
	protected function stacktrace($collection)
	{
		foreach($collection as $k => $v)
		{
			if(!empty($v['class']))
			{
				$collection[$k]['call'] = $v['class'] . $v['type'] . $v['function'];
			}
			else
			{
				$collection[$k]['call'] = $v['function'];
			}
			if(empty($v['file']))
			{
				$collection[$k]['file'] = '???';
			}
			if(empty($v['line']))
			{
				$collection[$k]['line'] = '???';
			}
			if(empty($v['args']))
			{
				$collection[$k]['args'] = array();
			}
		}
		return $collection;
	}
}

/**
 * Exteption handler.
 *
 * @package FinallyPHP.error
 * 
 * @param Exception $exception Exception class.
 */
function exception_handler($exception)
{
	if (!in_array(get_class($exception), array('Error', 'Response')))
	{
		$exception = new Error($exception->getCode(), $exception->getMessage());
	}
	
	$exception->log();
	$exception->output();
}

set_exception_handler('exception_handler');
//set_error_handler('error_handler');
