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
 * SQLModel interface.
 *
 * @package FinallyPHP.model
 */
interface ISQLModel extends IBaseModel
{
	public static function init($server, $username, $password, $database);
	
	public static function describe();
	public static function get_foreign_keyname();
}

/**
 * SQLModel proxy interface.
 *
 * @package FinallyPHP.model
 */
interface ISQLModelProxy
{
	public static function count(array $p = null);
	public static function find($where = null, $order = null, $order_mode = null);
	public static function get(array $p = null);
	public static function set(array $data, array $p = null);
	public static function delete(array $p = null);
}

/**
 * Abstract SQL Model class. Base for all SQL-based model types.
 *
 * SQLModel requires the FinallyPHP MySQL library, or a different
 * one that matches its interface, with the 'SQL' alias.
 *
 * SQLModel requires to be initialized with SQL and db credentials
 * before subclasses can work.
 *
 * @package FinallyPHP.model
 * @see MySQLController
 * @see SQLModel#init
 */
abstract class SQLModel extends BaseModel implements ISQLModel, ISQLModelProxy
{
	/**
	 * SQL server.
	 *
	 * @var string
	 */
	protected static $server;
	/**
	 * SQL database.
	 *
	 * @var string
	 */
	protected static $database;
	/**
	 * SQL table.
	 *
	 * The model table must be set in all SQLModel subclasses,
	 * unless in a single table inheritance scenario.
	 *
	 * @var string
	 */
	protected static $table;
	
	/**
	 * Table primary key.
	 *
	 * @var string
	 */
	protected static $key = 'id';
	/**
	 * Db table primary key increment.
	 *
	 * @var bool
	 */
	protected static $key_increment = true;
	
	/**
	 * SQL init.
	 *
	 * If a SQLModel subclass uses a different SQL server or database,
	 * it needs to be initialized separately with the respective data.
	 * If that is the case, the $server and $database properties also
	 * need to be defined within the class body, without any values.
	 *
	 * Example: SQLSubClass::init(,,,)
	 *
	 * @see MySQLController#register_conn
	 *
	 * @param string $server   SQL server
	 * @param string $username SQL username
	 * @param string $password SQL password
	 * @param string $database SQL database
	 */
	public static function init($server, $username, $password, $database)
	{
		static::$server   = $server;
		static::$database = $database;
		
		SQL::register_conn(array(
			'server'   => $server,
			'username' => $username,
			'password' => $password
		));
		
		mysql_query('SET NAMES "utf8"');
	}
	
	/**
	 * Model (table) describe.
	 *
	 * @return array Model structure
	 */
	public static function describe()
	{
		$query = sprintf('DESCRIBE `%s`', SQL::escape(static::$table));
		
		$result = SQL::multi_get(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
		
		return $result;
	}
	
	/**
	 * Foreign key name image. Used for "magic" join detection.
	 * 
	 * Generates a foreign key name, as seem from other tables
	 * that have a foreign key pointing to this table.
	 * 
	 * Must follow the following convention: "$modelname_$key".
	 * So if this model's class name was 'User' and would have
	 * a primary key named 'id', its foreign key image would be
	 * "user_id". Lowercase.
	 * 
	 * @return string Foreign-looking key name
	 */
	public static function get_foreign_keyname()
	{
		return sprintf('%s_%s',
			str\underscore_from_camelcase(static::base_classname()),
			static::$key
		);
	}
	
	/**
	 * Model selection count.
	 *
	 * @see BaseModel#count
	 * @see MySQLController#generate_where_clause
	 *
	 * @param  array $where Search filter conditions
	 * @return   int        Item count
	 */
	public static function count(array $p = null)
	{
		p($p, array(
			'field'      => '*',
			'where'      => null,
			'order'      => null,
			'order_mode' => null,
			'offset'     => null,
			'limit'      => null,
			'join'       => null
		));
		
		$p['where'] = static::get_real_where($p['where']);
		
		$models = array(array(null, static::classname()));
		
		$query = SQL::generate_aggregate(array(
			'table'     => static::$table,
			'operation' => 'COUNT',
			'field'     => $p['field'],
			'where'     => $p['where'],
			'join'      => static::generate_joins($p['join'], $models)
		));
		
		$result = SQL::get(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
		
		return intval(isset($result['count']) ? $result['count'] : 0);
	}
	/**
	* Model selection sum.
	*
	* @see BaseModel#sum
	* @see MySQLController#generate_where_clause
	*
	* @param  array $where Search filter conditions
	* @return   int        Item count
	*/
	public static function sum(array $p = null)
	{
		p($p, array(
			'field'      => REQUIRED_PARAM,
			'where'      => null,
			'order'      => null,
			'order_mode' => null,
			'offset'     => null,
			'limit'      => null,
			'join'       => null
		));
		
		$p['where'] = static::get_real_where($p['where']);
		
		$models = array(array(null, static::classname()));
		
		$query = SQL::generate_aggregate(array(
			'table' => static::$table,
			'operation' => 'SUM',
			'field' => $p['field'],
			'where' => $p['where'],
			'join' => static::generate_joins($p['join'], $models)
		));
		
		$result = SQL::get(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
		
		return intval(isset($result['count']) ? $result['count'] : 0);
	}
	
	/**
	 * Model item search.
	 *
	 * @see BaseModel#find
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 *
	 * @param   array $where      Search filter conditions
	 * @param  string $order      Search order field
	 * @param  string $order_mode Search order mode
	 * @return  Model             Found model
	 */
	public static function find($where = null, $order = null, $order_mode = null)
	{
		$where = static::get_real_where($where);
		
		$query = SQL::generate_select(array(
			'table'      => static::$table,
			'where'      => $where,
			'order'      => $order,
			'order_mode' => $order_mode,
			'offset'     => null,
			'limit'      => 1
		));
		
		$result = SQL::get(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
		
		return parent::find($result);
	}
	
	/**
	 * Model collection search.
	 *
	 * Possible parameters: where, order, order_mode, offset, limit, join.
	 *
	 * @see BaseModel#get
	 * @see SQLModel#generate_joins
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 * @see MySQLController#generate_join_clause
	 *
	 * @param   array $p Filter parameters
	 * @return  array    Matched model collection
	 */
	public static function get(array $p = null)
	{
		p($p, array(
			'where'      => null,
			'order'      => null,
			'order_mode' => null,
			'offset'     => null,
			'limit'      => null,
			'join'       => null
		));
		
		$p['where'] = static::get_real_where($p['where']);
		
		$models = array(array(null, static::classname()));
		
		$query = SQL::generate_select(array(
			'table'      => static::$table,
			'where'      => $p['where'],
			'order'      => $p['order'],
			'order_mode' => $p['order_mode'],
			'offset'     => $p['offset'],
			'limit'      => $p['limit'],
			'join'       => static::generate_joins($p['join'], $models)
		));
		
		$results = SQL::multi_get(array(
			'server'       => static::$server,
			'database'     => static::$database,
			'query'        => $query,
			'result_type'  => count($models) > 1 ? MYSQL_NUM : MYSQL_ASSOC
		));
		if(!$results)
		{
			$results = array();
		}
		
		if(count($models) > 1)
		{
			return static::get_tree($results, $models, static::classname());
		}
		return parent::get($results);
	}
	
	/**
	 * Model collection update.
	 *
	 * Possible parameters: where, order, order_mode, offset, limit.
	 *
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $data Updated data
	 * @param   array $p    Filter parameters
	 * @return   bool       Update response
	 */
	public static function set(array $data, array $p = null)
	{
		p($p, array(
			'where'      => null,
			'order'      => static::$key,
			'order_mode' => DESC,
			'offset'     => null,
			'limit'      => null
		));
		
		$query = SQL::generate_update(array(
			'table'      => static::$table,
			'data'       => $data,
			'where'      => $p['where'],
			'order'      => $p['order'],
			'order_mode' => $p['order_mode'],
			'offset'     => $p['offset'],
			'limit'      => $p['limit']
		));
		
		return SQL::set(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
	}
	
	/**
	 * Model collection delete.
	 *
	 * Possible parameters: where, order, order_mode, offset, limit.
	 *
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $p Filter parameters
	 * @return   bool    Delete response
	 */
	public static function delete(array $p = null)
	{
		p($p, array(
			'where'      => null,
			'order'      => static::$key,
			'order_mode' => DESC,
			'offset'     => null,
			'limit'      => null
		));
		
		$query = SQL::generate_delete(array(
			'table'      => static::$table,
			'where'      => $p['where'],
			'order'      => $p['order'],
			'order_mode' => $p['order_mode'],
			'offset'     => $p['offset'],
			'limit'      => $p['limit']
		));
		
		return SQL::set(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		));
	}
	
	/**
	 * Join relationships generation.
	 *
	 * Transforms the requested joins in an associative array that
	 * the SQL library expects.
	 *
	 * Join syntax:
	 * "joined_table.child_table" and "joined_table@parent_table"
	 *
	 * @see SQLModel#generate_join
	 *
	 * @param  string|array $joins  Input join data
	 * @param         array $models Updating model collection, passed by reference
	 * @return        array         Output join data
	 */
	protected static function generate_joins($joins, array &$models)
	{
		if(!is_array($joins))
		{
			$joins = $joins ? array($joins) : array();
		}
		foreach($joins as $k => $v)
		{
			if(!preg_match('/([a-z0-9-_]+)(\.|@)([a-z0-9-_]+)/i', $v, $matches))
			{
				throw new Error(500, 'Invalid model join #1');
			}
			if($matches[2] == '.')
			{
				$parent_model = model\names::singular($matches[1], true);
				$child_model  = model\names::singular($matches[3], true);
				$joined_model = $parent_model;
				$model = array($child_model, $parent_model);
			}
			elseif($matches[2] == '@')
			{
				$parent_model = model\names::singular($matches[3], true);
				$child_model  = model\names::singular($matches[1], true);
				$joined_model = $child_model;
				$model = array($parent_model, $child_model);
			}
			if(empty($parent_model) || empty($child_model))
			{
				throw new Error(500, 'Invalid model join #2');
			}
			$joins[$k] = self::generate_join($joined_model, $parent_model, $child_model);
			
			array_push($models, $model);
		}
		
		return $joins;
	}
	
	/**
	 * Single join relationship generation.
	 *
	 * @param  string $joined Joined table name
	 * @param  string $parent Parent table name
	 * @param  string $child  Child table name
	 * @return  array         Output join data
	 */
	protected static function generate_join($joined, $parent, $child)
	{
		return array(
			'joined_table' => $joined::$table,
			'parent_table' => $parent::$table,
			'parent_key'   => $parent::$key,
			'child_table'  => $child::$table,
			'child_key'    => $parent::get_foreign_keyname()
		);
	}
	
	/**
	 * Join relationships sort callback.
	 *
	 * @see SQLModel#generate_join
	 *
	 * @param  array $a First sorted member
	 * @param  array $b Second sorted member
	 * @return   int    Sort result {-1, 0, 1}
	 */
	protected static function sort_joins($a, $b)
	{
		if($a['parent_table'] == $b['parent_table'])
		{
			return 0;
		}
		return ($a['parent_table'] < $b['parent_table']) ? -1 : 1;
	}
	
	/**
	 * Recursive model leveled-hierarchy build.
	 *
	 * @param   array $results One-dimensional SQL results
	 * @param   array $models  Hierarchy involved models
	 * @param  string $model   Model classname
	 * @param     int $level   Level iteration count, passed by reference
	 * @return  array          Hierarchic two-dimensional build.
	 */
	protected static function get_tree($results, $models, $model, &$level = 0)
	{
		$offset = $level;
		
		$branch = array();
		$subresults = array();
		foreach($results as $k => $result)
		{
			if(empty($subresults))
			{
				$data = array();
				
				$level = $offset;
				$key_index = null;
				
				$table_description = $model::describe();
				foreach($table_description as $k2 => $column)
				{
					if($column['Field'] == $model::$key)
					{
						$key_index = $offset + $k2;
					}
					$data[$column['Field']] = $result[$level++];
				}
				
				if($key_index === null)
				{
					throw new Error(500, 'Invalid model results structure');
				}
				if($data[$model::$key] === null)
				{
					continue;
				}
			}
			$subresults[] = $result;
			
			if($k + 1 >= count($results) || $result[$key_index] != $results[$k + 1][$key_index])
			{
				$real_model = $model::real_classname($data);
				$new_model = new $real_model($data, false);
				foreach($models as $m)
				{
					if($m[0] == $model)
					{
						list($s_key, $p_key) = self::get_keynames($m[1]);
						
						$new_model->cache($p_key,
							static::get_tree($subresults, $models, $m[1], $level)
						);
						if(count($new_model->$p_key) == 1)
						{
							$new_model->cache($s_key, $new_model->{$p_key}[0]);
						}
					}
				}
				$branch[] = $new_model;
				$subresults = array();
			}
		}
		return $branch;
	}
	
	/**
	 * Name helper. Returns both singular and plural forms, with underscores. 
	 *
	 * @param  string $model Model classname
	 * @return  array        Singular and plural model names
	 */
	protected static function get_keynames($model)
	{
		return array(
			str\underscore_from_camelcase($model),
			str\underscore_from_camelcase(model\names::plural($model))
		);
	}
	
	protected static function get_real_where($where)
	{
		if(static::base_classname() == static::classname())
		{
			return $where;
		}
		if(!$where)
		{
			return array(sprintf('`%s`.`%s` = "%s"',
				static::$table,
				static::$type_column,
				static::real_classname()
			));
		}
		if(!is_array($where))
		{
			$where = array($where);
		}
		$where[0] = sprintf('( %s ) AND `%s`.`%s` = "%s"',
			$where[0],
			static::$table,
			static::$type_column,
			static::real_classname()
		);
		return $where;
	}
	
	/**
	 * Magic method overload. Used for model relationships.
	 *
	 * @see BaseModel#__call
	 * @see SQLModel#get_children
	 * @see SQLModel#add_children
	 *
	 * @param  string $name      Method name
	 * @param   array $arguments Method arguments
	 * @return  mixed            Forwarded model action response
	 */
	
	public function __call($name, $arguments)
	{
		if(!$r = parent::__call($name, $arguments))
		{
			throw new Error(500, 'Invalid model method #2');
		}
		$method = $r['action'] . '_relative';
		if(method_exists($this, $method))
		{
			return $this->$method($r, $arguments);
		}
	}
	
	/**
	 * Destroy.
	 *
	 * @see BaseModel#destroy
	 *
	 * @return bool Destroy confirmation
	 */
	public function destroy()
	{
		$key = static::$key;
		
		$query = SQL::generate_delete(array(
			'table' => static::$table,
			'where' => array(sprintf('`%s` = $1', $key), $this->$key)
		));
		
		if(!SQL::set(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		)))
		{
			$this->add_error(mysql_error());
			return false;
		}
		
		return parent::destroy();
	}
	
	/**
	 * Relative select.
	 *
	 * @see SQLModel#__call
	 * @see SQLModel#find
	 * @see SQLModel#get
	 *
	 * @param  array $r         Relation data
	 * @param  array $arguments Operation arguments
	 * @return mixed            Found model children
	 */
	protected function get_relative($r, $arguments)
	{
		list($s_key, $p_key) = self::get_keynames($r['model_name']);
		
		if(!$r['many'])
		{
			$key = $r['model_name']::$key;
			$value = $this->{$r['model_name']::get_foreign_keyname()};
			
			$where = $this->get_relation($key, $value);
			
			$model = $r['model_name']::find($where);
			$this->cache($s_key, $model);
			
			return $model;
		}
		else
		{
			$params = !empty($arguments[0]) ? $arguments[0] : null;
			
			$key = static::get_foreign_keyname();
			$value = $this->{static::$key};
			
			$params = $this->get_related_params(
				$params,
				$this->get_relation($key, $value)
			);
			
			$models = $r['model_name']::get($params);
			$this->cache($p_key, $models);
			
			return $models;
		}
	}
	
	/**
	 * Relative add.
	 *
	 * @see SQLModel#__call
	 *
	 * @param  array $r         Relation data
	 * @param  array $arguments Operation arguments
	 * @return mixed            Added model children
	 */
	protected function add_relative($r, $arguments)
	{
		$data = $arguments[0];
		
		if(!$r['many'])
		{
			$data[static::get_foreign_keyname()] = $this->{static::$key};
			$new_model = new $r['model_name']($data);
			
			if(!$new_model->save())
			{
				$this->add_error($new_model->get_errors());
				return false;
			}
			return $new_model;
		}
		else
		{
			$items = array();
			foreach((array)$data as $item)
			{
				if(!is_array($item))
				{
					$this->add_error('Invalid model data');
					return false;
				}
				$item[static::get_foreign_keyname()] = $this->{static::$key};
				$new_model = new $r['model_name']($item);
				
				if(!$new_model->is_valid())
				{
					$this->add_error($new_model->get_errors());
					return false;
				}
				$items[] = $new_model;
			}
			foreach($items as $item)
			{
				$item->save();
			}
			return $items;
		}
	}
	
	/**
	 * Relative edit.
	 *
	 * @see SQLModel#__call
	 * @see SQLModel#set
	 *
	 * @param  array $r         Relation data
	 * @param  array $arguments Operation arguments
	 * @return mixed            Edit response
	 */
	protected function edit_relative($r, $arguments)
	{
		$data = $arguments[0];
		$params = !empty($arguments[1]) ? $arguments[1] : null;
		
		if(!$r['many'])
		{
			$key = $r['model_name']::$key;
			$value = $this->{$r['model_name']::get_foreign_keyname()};
		}
		else
		{
			$key = static::get_foreign_keyname();
			$value = $this->{static::$key};
		}
		if($value === null)
		{
			throw new Error(500, 'Invalid edit model method');
		}
		$params = $this->get_related_params(
			$params,
			$this->get_relation($key, $value)
		);
		
		return $r['model_name']::set($data, $params);
	}
	
	/**
	 * Relative delete.
	 *
	 * @see SQLModel#__call
	 * @see SQLModel#delete
	 *
	 * @param  array $r         Relation data
	 * @param  array $arguments Operation arguments
	 * @return mixed            Delete response
	 */
	protected function delete_relative($r, $arguments)
	{
		$params = !empty($arguments[0]) ? $arguments[0] : null;
		
		if(!$r['many'])
		{
			$key = $r['model_name']::$key;
			$value = $this->{$r['model_name']::get_foreign_keyname()};
		}
		else
		{
			$key = static::get_foreign_keyname();
			$value = $this->{static::$key};
		}
		if($value === null)
		{
			throw new Error(500, 'Invalid delete model method');
		}
		$params = $this->get_related_params(
			$params,
			$this->get_relation($key, $value)
		);
		
		return $r['model_name']::delete($params);
	}
	
	/**
	 * Relate parameters to relation.
	 *
	 * Combines the user parameters with the relationship condition.
	 *
	 * @param  array $params   User params
	 * @param  array $relation Relation condition
	 * @return array           Combined params
	 */
	protected function get_related_params($params, $relation)
	{
		if(!is_array($params))
		{
			$params = array();
		}
		if(!empty($params['where']) && is_string($params['where'][0]))
		{
			$params['where'][0] = sprintf('( %s ) AND ( %s )',
				$params['where'][0],
				preg_replace_callback('#\$([0-9]+)#', function($match) use ($params)
				{
					return '$' . strval($match[1] + count($params['where']) - 1);
				},
				$relation[0])
			);
			for($i = 1; $i < count($relation); $i++)
			{
				$params['where'][] = $relation[$i];
			}
		}
		else
		{
			$params['where'] = $relation;
		}
		return $params;
	}
	
	/**
	 * Relationship helper.
	 * 
	 * @param  string $key   Relation key
	 * @param  string $value Relation value
	 * @return  array        Relation condition
	 */
	protected function get_relation($key, $value)
	{
		return array("`$key` = $1", $value);
	}
	
	/**
	 * Create. Used when saving a new SQLModel instance.
	 *
	 * @see BaseModel#create
	 *
	 * @return bool Create confirmation
	 */
	protected function create()
	{
		$query = SQL::generate_insert(array(
			'table' => static::$table,
			'data'  => $this->_data
		));
		
		if(!SQL::set(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		)))
		{
			$this->add_error(mysql_error());
			return false;
		}
		
		if(static::$key_increment)
		{
			$key = static::$key;
			$this->$key = mysql_insert_id();
		}
		
		return parent::create();
	}
	
	/**
	 * Update. Used when saving an existing SQLModel instance.
	 *
	 * @see BaseModel#update
	 *
	 * @return bool Update confirmation
	 */
	protected function update()
	{
		if(!count($this->_modified_data))
		{
			return parent::update();
		}
		$key = static::$key;
		
		$query = SQL::generate_update(array(
			'table' => static::$table,
			'data'  => $this->_modified_data,
			'where' => array(sprintf('`%s` = $1', $key), $this->$key)
		));
		
		if(!SQL::set(array(
			'server'   => static::$server,
			'database' => static::$database,
			'query'    => $query
		)))
		{
			$this->add_error(mysql_error());
			return false;
		}
		
		return parent::update();
	}
}
