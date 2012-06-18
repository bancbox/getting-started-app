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
 * conf interface.
 *
 * @package FinallyPHP.core
 */
interface Iconf
{
	public static function is_defined($name);
	public static function are_defined(array $names);
	
	public static function set($name, $value);
	public static function get($name);
	public static function get_all();
}

/**
 * Static config container class.
 * Stores global variables.
 *
 * As opposed to the PHP constants, they can be redefined at runtime
 * and can also contain other conf var references. Meaning that when
 * the referenced variable changes, the initial one does as well.
 *
 * @package FinallyPHP.core
 */
abstract class conf implements Iconf
{
	/**
	 * Sub-variable reference regexp.
	 *
	 * @var string
	 */
	const var_pattern = '/{([a-z0-9-_]+)}/i';
	/**
	 * Variable container.
	 *
	 * @var array
	 */
	private static $stack = array();
	
	/**
	 * Var existance check.
	 *
	 * @param  string $name Var name
	 * @return   bool       Check result
	 */
	public static function is_defined($name)
	{
		return $name && isset(self::$stack[$name]) ? true : false;
	}
	
	/**
	 * Var existance mass check.
	 *
	 * @param  array $names Var names
	 * @return  bool        Check result
	 */
	public static function are_defined(array $names)
	{
		foreach($names as $name)
		{
			if(!self::is_defined($name))
			{
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Var set.
	 *
	 * To include a var reference in another var, use {VAR_NAME}.
	 * 
	 * Example: conf::set('VAR_NAME', "{OTHER_VAR_NAME} plus more")
	 *
	 * @param string $name  Var name
	 * @param  mixed $value Var value
	 */
	public static function set($name, $value)
	{
		if(!strlen($name))
		{
			throw new Error(500, 'Invalid config var name');
		}
		self::$stack[$name] = $value;
	}
	
	/**
	 * Var get.
	 *
	 * Example: conf::get('VAR_NAME')
	 *
	 * @param  string $name Var name
	 * @return  mixed       Var value
	 */
	public static function get($name)
	{
		if(!self::is_defined($name))
		{
			throw new Error(500, sprintf('Invalid config var "%s"', $name));
		}
		$value = self::$stack[$name];
		
		if(is_string($value))
		{
			return preg_replace_callback(
				self::var_pattern,
				array('conf', 'replace_callback'),
				$value
			);
		}
		return $value;
	}
	
	/**
	 * Var mass get.
	 *
	 * @return array All vars
	 */
	public static function get_all($pattern = null)
	{
		if ($pattern)
		{
			$matches = array();
			foreach (self::$stack as $key => $value)
			{
				if (preg_match('/'.$pattern.'/', $key))
				{
					$k = str_replace($pattern, '', $key);
					$matches[$k] = self::get($key);
				}
			}
			return $matches;
		}
		else
		{
			return self::$stack;
		}
	}
	
	/**
	 * Internal var reference replace callback.
	 *
	 * @return mixed Matched var value
	 */
	private static function replace_callback($matches)
	{
		if(self::is_defined($matches[1]))
		{
			return self::get($matches[1]);
		}
		return '{' . $matches[1] . '}';
	}
}
