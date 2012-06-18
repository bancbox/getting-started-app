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
 * View interface.
 * 
 * @package FinallyPHP.view
 */
interface IView
{
	public static function partial_render($name, array $data = array());
	public static function path_render($path, array $data = array(), $layout_path = null);
	
	public function __call($name, $arguments);
	public function __construct();
	
	public function load_path($path, array $data = array());
	public function load_content($content);
}

/**
 * View loader class.
 * 
 * @package FinallyPHP.view
 */
class View implements IView
{
	/**
	 * Static partial render.
	 *
	 * @see View#render_partial
	 *
	 * @param  string $name Partial name
	 * @param   array $data Partial data
	 * @return string       Rendered partial contents
	 */
	public static function partial_render($name, array $data = array())
	{
		$view = new View();
		
		return $view->render_partial($name, $data);
	}
	
	/**
	 * Static view render, by path.
	 *
	 * @see View#render
	 *
	 * @param  string $name   View path
	 * @param   array $data   View data
	 * @param  string $layout Layout path
	 * @return string         Rendered view contents
	 */
	public static function path_render($path, array $data = array(), $layout_path = null)
	{
		$view = new View();
		
		return $view->render($path, $data, $layout_path);
	}
	
	/**
	 * Status code header.
	 * 
	 * Usually set from an error code.
	 *
	 * @var int
	 */
	public $status_code;
	/**
	 * Content type header.
	 * 
	 * Taken from the config file.
	 *
	 * @var string
	 */
	public $content_type;
	
	/**
	 * View data.
	 *
	 * @var array
	 */
	protected $data = array();
	/**
	 * View content.
	 *
	 * @var string
	 */
	protected $content = null;
	
	/**
	 * Magic method overload. Used for View helpers.
	 *
	 * You can use helpers from views through the $this keyword.
	 * Example: $this->helper_function()
	 *
	 * @param  string $name      Method name
	 * @param   array $arguments Method arguments
	 * @return  mixed            Helper function response
	 */
	public function __call($name, $arguments)
	{
		$function_name = 'helper\\' . $name;
		if(function_exists($function_name))
		{
			return call_user_func_array($function_name, $arguments);
		}
		throw new Error(500, sprintf('Helper method "%s" does not exist', $function_name));
	}
	
	/**
	 * View constructor.
	 *
	 * @see View#load_helpers
	 */
	public function __construct()
	{
		$this->load_helpers();
	}
	
	/**
	 * Path load.
	 *
	 * @see View#render
	 * @see View#load_content
	 *
	 * @param string $path   View path
	 * @param  array $data   View data
	 * @param string $layout Layout path
	 */
	public function load_path($path, array $data = array(), $layout_path = null)
	{
		$content = $this->render($path, $data, $layout_path);
		if(!is_string($content))
		{
			throw new Error(404, 'No view');
		}
		$this->load_content($content);
	}
	
	/**
	 * Content load and output.
	 *
	 * @see View#set_header
	 *
	 * @param string $path View contents
	 */
	public function load_content($content)
	{
		while(ob_get_level())
		{
			ob_end_clean();
		}
		$this->set_header();
		
		echo $content;
		exit;
	}
	
	/**
	 * Partial output.
	 *
	 * @see View#render_partial
	 *
	 * @param string $name      Partial name
	 * @param  array $data      Partial data
	 * @param   bool $view_data View data inclusion
	 */
	protected function partial($name, array $data = null, $view_data = false)
	{
		echo $this->render_partial($name, $data, $view_data);
	}
	
	/**
	 * Partial render.
	 *
	 * @see FinallyPHP.view.get_partial_path
	 * @see View#render_path
	 *
	 * @param  string $name      Partial name
	 * @param   array $data      Partial data
	 * @param    bool $view_data View data inclusion
	 * @return string            Rendered partial contents
	 */
	protected function render_partial($name, array $data = null, $view_data = true)
	{
		if($partial_path = view\get_partial_path($name))
		{
			if($view_data)
			{
				$data = array_merge($this->data, (array)$data);
			}
			elseif(is_null($data))
			{
				$data = array();
			}
			$current_data = $this->data;
			
			$this->data = $data;
			$contents = $this->render_path($partial_path, $data);
			$this->data = $current_data;
			
			return $contents;
		}
		return null;
	}
	
	/**
	 * View render.
	 *
	 * @see View#render_path
	 *
	 * @param  string $path   View path
	 * @param   array $data   View data
	 * @param  string $layout Layout path
	 * @return string         Rendered view contents
	 */
	protected function render($path, array $data = array(), $layout_path = null)
	{
		$this->data = $data;
		$this->content = $this->render_path($path, $this->data);
		
		if(is_string($this->content) && $layout_path)
		{
			$this->content = $this->render_path($layout_path, $this->data);
		}
		return $this->content;
	}
	
	/**
	 * File path render.
	 *
	 * @param  string $path File path
	 * @param   array $data Data
	 * @return string       Rendered file contents
	 */
	protected function render_path($path, array $data = array())
	{
		if(!$path || !file_exists($path))
		{
			return null;
		}
		ob_start();
		
		extract($data, EXTR_REFS);
		include($path);
		
		return ob_get_clean();
	}
	
	/**
	 * Helper includes.
	 *
	 */
	protected function load_helpers()
	{
		file\require_more(conf::get('HELPER_PATH'));
		file\require_more(conf::get('APP_HELPER_PATH'));
	}
	
	/**
	 * Header setup.
	 *
	 */
	protected function set_header()
	{
		if(!empty($this->content_type))
		{
			header('Content-Type: ' . $this->content_type);
		}
		if(!empty($this->status_code))
		{
			header('HTTP', true, $this->status_code);
		}
	}
}
