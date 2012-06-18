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
 * BaseController interface.
 * 
 * @package FinallyPHP.controller
 */
interface IBaseController
{
	public function __construct();
	
	public function constructor();
	public function destructor();
}

/**
 * Abstract Controller class; base for all controllers.
 * 
 * @package FinallyPHP.controller
 */
abstract class BaseController implements IBaseController
{
	/**
	 * Controller default action.
	 *
	 * @var string
	 */
	public static $default_action = null;
	/**
	 * Controller arguments.
	 *
	 * @var array
	 */
	public $args = array();
	/**
	 * Page header status code.
	 *
	 * @var int
	 */
	public $status_code;
	/**
	 * Assigned view data from the controller.
	 *
	 * @var array
	 */
	public $view_data;
	/**
	 * Assigned referenced view data from the controller.
	 *
	 * @var array
	 */
	public $ref_view_data;
	/**
	 * View layout name (same as file name).
	 *
	 * @var string
	 */
	public $view_layout;
	
	/**
	 * Controller constructor.
	 *
	 */
	public function __construct()
	{
		$this->view_data = array();
		$this->ref_view_data = array();
	}
	
	/**
	 * Controller virtual constuctor.
	 * Called before action but after __construct().
	 * 
	 * Simplifies having global controller logic, before the action,
	 * without redeclaring the internal constructor.
	 */
	public function constructor()
	{
		$this->assign_defaults();
	}
	
	/**
	 * Controller virtual destructor.
	 * Called after action but before __destructor().
	 *
	 * Simplifies having a global controller logic, after the action,
	 * but before the view is rendered, since the internal destructor
	 * is called when everything finished rendering, and PHP releases
	 * all the variables.
	 */
	public function destructor()
	{
		
	}
	
	/**
	 * Controller argument set.
	 *
	 * @param  string $name     Argument name
	 * @param    bool $fallback Fallback argument value, if not set
	 * @return  mixed           Argument value
	 */
	protected function arg($name, $fallback = null)
	{
		if(isset($this->args[$name]))
		{
			return $this->args[$name];
		}
		return $fallback;
	}
	
	/**
	 * View var set.
	 *
	 * @param string $name  Var name
	 * @param  mixed $value Var value
	 */
	protected function assign($name, $value)
	{
		$this->view_data[$name] = $value;
	}
	
	/**
	 * View var referenced set.
	 *
	 * @param string $name  Var name
	 * @param  mixed $value Var value
	 */
	protected function assign_by_ref($name, &$value)
	{
		$this->ref_view_data[$name] = &$value;
		$this->view_data[$name] = &$this->ref_view_data[$name];
	}
	
	/**
	 * View var mass asignment.
	 *
	 * @param array $data Var data
	 */
	protected function assign_more(array $data)
	{
		foreach($data as $k => $v)
		{
			$this->assign($k, $v);
		}
	}
	
	/**
	 * Pre-defined View vars.
	 * 
	 */
	protected function assign_defaults()
	{
		$this->assign_more(array(
			'app'        => conf::get('APP'),
			'controller' => conf::get('CONTROLLER'),
			'action'     => conf::get('ACTION'),
			'extension'  => conf::get('EXTENSION'),
			
			'view_path'     => conf::get('VIEW_PATH'),
			'app_view_path' => conf::get('APP_VIEW_PATH'),
			
			'url' => conf::get('URL')
		));
	}
	
	/**
	 * Internal redirect.
	 * 
	 * Alias for url\internal(), with auto redirecting.
	 */
	protected function goto_page($action = null, $controller = null, $app = null, $extension = null, $extra_query = null)
	{
		$url = url\internal($action, $controller, $app, $extension, $extra_query);
		$this->goto_url($url);
	}
	
	/**
	 * External redirect.
	 * 
	 * Alias for url\redirect().
	 */
	protected function goto_url($url)
	{
		url\redirect($url);
	}
}
