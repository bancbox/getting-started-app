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
 * BaseModel interface.
 * 
 * @package FinallyPHP.model
 */
interface IBaseModel
{
	public static function classname();
	public static function real_classname($data = null);
	
	public function __construct(array $data = array(), $new = true);
	public function __set($name, $value);
	public function __get($name);
	public function __call($name, $arguments);
	
	public function edit(array $data);
	public function cache($name, $value);
	public function save();
	public function destroy();
	
	public function is_valid();
	public function get_errors($string = false);
}

/**
 * BaseModel proxy interface.
 * 
 * @package FinallyPHP.model
 */
interface IBaseModelProxy
{
	public static function find($data);
	public static function get(array $data_collection = array());
}

/**
 * Abstract Model class. Base for all model types.
 * 
 * @package FinallyPHP.model
 */
abstract class BaseModel implements IBaseModel, IBaseModelProxy
{
	/**
	 * Allowed fields within a mass assignment.
	 *
	 * @var array
	 */
	protected static $accessible_fields = null;
	
	/**
	 * Class type column name.
	 *
	 * Used for single table inheritance.
	 *
	 * @var string
	 */
	protected static $type_column = null;
	
	/**
	 * Class name get.
	 *
	 * Based on PHP 5.3's Late Static Binding.
	 *
	 * @return string Class name
	 */
	public static function classname()
	{
		return get_called_class();
	}
	
	/**
	 * Base class name get.
	 *
	 * Parent class for single table inheritance,
	 * based on the $type_column field.
	 *
	 * @return string Class name
	 */
	public static function base_classname()
	{
		$class = static::classname();
		while($parent = get_parent_class($class))
		{
			if(!$parent::$type_column)
			{
				break;
			}
			$class = $parent;
		}
		return $class;
	}
	
	/**
	 * Real class name get.
	 *
	 * Child class for single table inheritance,
	 * based on the $type_column field.
	 *
	 * @return string Class name
	 */
	public static function real_classname($data = null)
	{
		$name = static::classname();
		if(static::$type_column && $data && !empty($data[static::$type_column]))
		{
			$real_name = $data[static::$type_column];
			if(\model\exists($real_name))
			{
				return $real_name;
			}
		}
		return $name;
	}
	
	/**
	 * Model item search.
	 *
	 * @param  array $data Model data
	 * @return Model       Found model
	 */
	public static function find($data)
	{
		$classname = static::real_classname($data);
		return is_array($data) ? new $classname($data, false) : null;
	}
	
	/**
	 * Model collection search.
	 *
	 * @param  array $data_collection Model data collection
	 * @return array                  Matched model collection
	 */
	public static function get(array $data_collection = array())
	{
		$items = array();
		foreach($data_collection as $k => $v)
		{
			$classname = static::real_classname($v);
			$items[] = new $classname($v, false);
		}
		return $items;
	}
	
	/**
	 * Instance type.
	 *
	 * @var bool
	 */
	protected $_is_new = true;
	/**
	 * Model data.
	 *
	 * @var array
	 */
	protected $_data;
	/**
	 * Added model data since last save.
	 *
	 * @var array
	 */
	protected $_modified_data;
	/**
	 * Cached relative model data.
	 *
	 * @var array
	 */
	protected $_cache;
	/**
	 * Instance errors.
	 *
	 * @var array
	 */
	protected $_errors = array();
	
	/**
	 * Model constructor.
	 *
	 * @param array $data Model data
	 * @param  bool $new  Instance type, new or existant
	 */
	public function __construct(array $data = array(), $new = true)
	{
		$this->_is_new = $new;
		
		$this->_data = array();
		$this->set_more($data, $new);
		
		if(!$this->_is_new)
		{
			$this->_modified_data = array();
		}
		elseif(static::$type_column)
		{
			$this->{static::$type_column} = get_class($this);
		}
	}
	
	/**
	 * Magic property get. Checks for custom getters.
	 *
	 * @param  string $name Property name
	 * @return  mixed       Property value
	 */
	public function __get($name)
	{
		$getter = "get_$name";
		if(isset($this->_cache[$name]))
		{
			return $this->_cache[$name];
		}
		if(method_exists($this, $getter))
		{
			return $this->$getter();
		}
		return $this->fetch($name);
	}
	
	/**
	 * Magic property set. Checks for custom setters.
	 *
	 * @param  string $name  Property name
	 * @param   mixed $value Property value
	 * @return   bool        Set confirmation
	 */
	public function __set($name, $value)
	{
		$setter = "set_$name";
		if(method_exists($this, $setter))
		{
			return $this->$setter($value);
		}
		return $this->assign($name, $value);
	}
	
	/**
	 * Magic method overload. Used for model relationships.
	 *
	 * @param  string $name      Method name
	 * @param   array $arguments Method arguments
	 * @return  array            Identified relationship data
	 */
	public function __call($name, $arguments)
	{
		start:
		if(preg_match('/^(get|add|edit|delete)_([a-z0-9_]+)$/', $name, $matches))
		{
			$many = false;
			$action = $matches[1];
			$model_name = str\camelcase_from_underscore($matches[2]);
			
			if(model\names::is_plural($model_name))
			{
				$many = true;
				$model_name = model\names::singular($model_name);
			}
			
			if(model\exists($model_name))
			{
				return array(
					'many'       => $many,
					'action'     => $action,
					'model_name' => $model_name
				);
			}
		}
		elseif(preg_match('/^[a-z0-9-_]+$/', $name))
		{
			$name = "get_$name";
			goto start;
		}
		throw new Error(500, 'Invalid model method');
	}
	
	/**
	 * Edit (mass assignment).
	 *
	 * @param array $data New model data
	 */
	public function edit(array $data)
	{
		$this->set_more($data, true);
	}
	
	/**
	 * Relative model data set.
	 *
	 * @param  string $name  Model name
	 * @param   mixed $value Model value
	 */
	public function cache($name, $value)
	{
		$this->_cache[$name] = $value;
	}
	
	/**
	 * Save. Validates before saving.
	 *
	 * @see BaseModel#create
	 * @see BaseModel#update
	 *
	 * @return bool Save and validation confirmation
	 */
	public function save()
	{
		if(!$this->is_valid())
		{
			return false;
		}
		return $this->_is_new ? $this->create() : $this->update();
	}
	
	/**
	 * Destroy.
	 *
	 * @return bool Destroy confirmation
	 */
	public function destroy()
	{
		return true;
	}
	
	/**
	 * Validation.
	 *
	 * @return bool Validation result
	 */
	public function is_valid()
	{
		$this->_errors = array();
		
		$methods = get_class_methods($this);
		foreach($methods as $method)
		{
			if(preg_match('/^validate_(.*)$/', $method, $matches))
			{
				$property = $matches[1];
				if(!$this->_is_new && !array_key_exists($property, $this->_modified_data))
				{
					continue;
				}
				if(!$this->$method($this->$property))
				{
					$this->add_error($property);
				}
			}
		}
		return !count($this->_errors);
	}
	
	/**
	 * Error get.
	 *
	 * @param          bool $string String format trigger
	 * @return string|array         Error content
	 */
	public function get_errors($string = false)
	{
		return $string ? implode(', ', $this->_errors) : $this->_errors;
	}
	
	/**
	 * Error add.
	 *
	 * @param string $name Error value
	 */
	protected function add_error($value)
	{
		if(!is_array($value))
		{
			$value = array($value);
		}
		$this->_errors = array_merge($this->_errors, $value);
	}
	
	/**
	 * Internal mass assignment. Checks for custom setters.
	 *
	 * @param array $data   New model data
	 * @param  bool $strict Assignment type, accessible fields and setter trigger
	 */
	protected function set_more(array $data, $strict = true)
	{
		$fields = static::$accessible_fields;
		foreach($data as $k => $v)
		{
			if(!$strict)
			{
				$this->assign($k, $v);
			}
			elseif($fields === null || in_array($k, $fields))
			{
				$this->$k = $v;
			}
		}
	}
	
	/**
	 * Direct property get.
	 *
	 * @param  string $name Property name
	 * @return  mixed       Property value
	 */
	protected function fetch($name)
	{
		return isset($this->_data[$name]) ? $this->_data[$name] : null;
	}
	
	/**
	 * Direct property set.
	 *
	 * @param  string $name  Property name
	 * @param   mixed $value Property value
	 * @return   bool        Set confirmation
	 */
	protected function assign($name, $value)
	{
		$this->_data[$name] = $value;
		$this->_modified_data[$name] = $value;
		
		return true;
	}
	
	/**
	 * Create. Used when saving a new Model instance.
	 *
	 * @return bool Create confirmation
	 */
	protected function create()
	{
		$this->_is_new = false;
		$this->_modified_data = array();
		
		return true;
	}
	
	/**
	 * Update. Used when saving an existing Model instance.
	 *
	 * @return bool Update confirmation
	 */
	protected function update()
	{
		$this->_modified_data = array();
		
		return true;
	}
}
