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

namespace model;

/**
 * Model names interface.
 *
 * @package FinallyPHP.model
 */
interface Inames
{
	public static function index($path);
	
	public static function add($name);
	public static function generate_plural($name);
	
	public static function is_singular($name);
	public static function is_plural($name);
	
	public static function singular($name, $convert = false);
	public static function plural($name, $convert = false);
}

/**
 * Static class for model names and relationships
 *
 * @package FinallyPHP.model
 */
abstract class names implements Inames
{
	/**
	 * Indexed models.
	 *
	 * Format: $models['singular'] = 'plural'
	 *
	 * @var array
	 */
	private static $models = array();
	
	/**
	 * Model name (noun) patterns.
	 * Regular and irregular.
	 *
	 * @var array
	 */
	private static $patterns = array
	(
		'(.*)' => '$1s',
		'(.*(s|ch|sh))' => '$1es',
		'(.*[^aeiou])y' => '$1ies',
		'(.*)x' => '$1ces',
		
		'child' => 'children',
		'foot'  => 'feet',
		'man'   => 'men',
		'mouse' => 'mice',
		'tooth' => 'teeth',
		'woman' => 'women'
	);
	
	/**
	 * Index. Recursive.
	 *
	 * Indexes all matched model files under the specified path.
	 *
	 * @param string $path Model files root path
	 */
	public static function index($path)
	{
		if($files = glob("$path/*.php"))
		{
			foreach($files as $file)
			{
				if(preg_match('/([a-z0-9-_\.]+)\.php$/i', $file, $matches))
				{
					self::add(\str\camelcase_from_hyphen($matches[1]));
				}
			}
		}
		if($dirs = glob("$path/*", GLOB_ONLYDIR))
		{
			foreach($dirs as $dir)
			{
				self::index($dir);
			}
		}
	}
	
	/**
	 * Model add (index).
	 * Automatically generates plural form.
	 *
	 * @param string $name Model name, singular form
	 */
	public static function add($name)
	{
		static::$models[$name] = self::generate_plural($name);
	}
	
	/**
	 * Plural generation.
	 *
	 * @param  string $name Singular form
	 * @return string       Plural form
	 */
	public static function generate_plural($name)
	{
		preg_match('/^(.*)([A-Z][^A-Z]+)$/', ucfirst($name), $matches);
		$word = $matches[2];
		
		foreach(self::$patterns as $pattern => $replacement)
		{
			$regexp = "/^$pattern$/i";
			if(preg_match($regexp, $matches[2]))
			{
				$word = ucfirst(preg_replace($regexp, $replacement, $matches[2]));
			}
		}
		return $matches[1] . $word;
	}
	
	/**
	 * Singular form check.
	 *
	 * @param  string $name Model name
	 * @return   bool       Check result
	 */
	public static function is_singular($name)
	{
		return isset(self::$models[$name]);
	}
	
	/**
	 * Plural form check.
	 * 
	 * @param  string $name Model name
	 * @return   bool       Check result
	 */
	public static function is_plural($name)
	{
		return in_array($name, self::$models);
	}
	
	/**
	 * Singular form casting.
	 *
	 * @param  string $name    Model name
	 * @param    bool $convert CamelCase convertion
	 * @return string          Singular form
	 */
	public static function singular($name, $convert = false)
	{
		if($convert)
		{
			$name = \str\camelcase_from_underscore($name);
		}
		if(self::is_plural($name))
		{
			return array_search($name, self::$models);
		}
		throw new \Error(500, 'Invalid model name #2');
	}
	
	/**
	 * Plural form casting.
	 *
	 * @param  string $name    Model name
	 * @param    bool $convert CamelCase convertion
	 * @return string          Plural form (already generated and indexed)
	 */
	public static function plural($name, $convert = false)
	{
		if($convert)
		{
			$name = \str\camelcase_from_underscore($name);
		}
		if(self::is_singular($name))
		{
			return self::$models[$name];
		}
		throw new \Error(500, 'Invalid model name #1');
	}
}
