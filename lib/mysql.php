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
 * MySQLController interface.
 * 
 * @package FinallyPHP.sql
 */
interface IMySQLController
{
	public static function get_default_server();
	public static function set_default_server($server);
	
	public static function register_conn($p = null);
	public static function conn_registered($server = null);
	public static function get_conn($p = null);
	
	public static function select_db($p = null);
	
	public static function count($p = null);
	public static function get($p = null);
	public static function multi_get($p = null);
	public static function set($p = null);
	
	public static function generate_select($p = null);
	public static function generate_insert($p = null);
	public static function generate_update($p = null);
	public static function generate_delete($p = null);
	
	public static function generate_join_clause(array $tables = null);
	public static function generate_set_clause($data);
	public static function generate_where_clause($where = null);
	public static function generate_order_clause($table, $order = null, $order_mode = ASC);
	public static function generate_limit_clause($offset = 0, $limit = null);
	
	public static function escape($string);
}

/**
 * MySQL static controller class.
 *
 * Strictly depends on the MySQLConnection class.
 * Together they form the default (My)SQL library for FinallyPHP.
 * Library on which the SQLModel depends.
 *
 * If you want to create an alternative SQL library for the
 * SQLModel, just make sure you follow this one's interface.
 *
 * Supports multiple server connections simultaneously,
 * juggling through MySQLConnection instances.
 *
 * @package FinallyPHP.sql
 * @see MySQLConnection
 * @see SQLModel
 */
abstract class MySQLController implements IMySQLController
{
	/**
	 * Connection collection, stores MySQLConnection instances.
	 *
	 * @var array
	 */
	private static $connections;
	/**
	 * Default server, selected when none is specified.
	 *
	 * Initially set by the first connection registered.
	 *
	 * @var string
	 */
	private static $default_server = null;
	
	/**
	 * SQL magic values.
	 *
	 * Within a query assignment, this values are not escaped,
	 * nor wrapped in quotes.
	 *
	 * @var array
	 */
	private static $magic_values = array('NULL', 'NOW()');
	
	/**
	 * Default server get.
	 *
	 * @return string Server address
	 */
	public static function get_default_server()
	{
		return self::$default_server;
	}
	
	/**
	 * Default server set.
	 *
	 * Checks whether the specified server was previously registered,
	 * otherwise it fails.
	 *
	 * @return bool Set result
	 */
	public static function set_default_server($server)
	{
		if(isset(self::$connections[$server]))
		{
			self::$default_server = $server;
			return true;
		}
		return false;
	}
	
	/**
	 * Connection register.
	 *
	 * Pushes a new MySQLConnection instance in the connections array
	 * and sets it the default server if null.
	 *
	 * Possible parameters: server, username, password, strict.
	 * Required parameters: server, username.
	 *
	 * @see p
	 * @see MySQLConnection#MySQLConnection
	 *
	 * @param     array $p Parameters
	 * @return resource    Created server link identifier
	 */
	public static function register_conn($p = null)
	{
		p($p, array(
			'server'   => REQUIRED_PARAM,
			'username' => REQUIRED_PARAM,
			'password' => '',
			'strict'   => true
		));
		
		if(!empty(self::$connections) && !empty(self::$connections[$p['server']]))
		{
			return self::$connections[$p['server']]->get_link();
		}
		$new_conn = new MySQLConnection(
			$p['server'], $p['username'], $p['password'], $p['strict']
		);
		if($new_conn_link = $new_conn->get_link())
		{
			if(empty(self::$connections))
			{
				self::$connections = array();
				self::$default_server = $p['server'];
			}
			self::$connections[$p['server']] = $new_conn;
		}
		return $new_conn_link;
	}
	
	/**
	 * Connection register check.
	 *
	 * Checks whether a certain server connection is registered if
	 * specified, or if any connection is registered otherwise.
	 *
	 * @param  string $server Server address to compare
	 * @return   bool         Check result
	 */
	public static function conn_registered($server = null)
	{
		if(is_null($server))
		{
			$server = self::get_default_server();
		}
		return ($server && !empty(self::$connections[$server]));
	}
	
	/**
	 * Connection get.
	 *
	 * Returns a certain server connection if specified,
	 * or the default one otherwise, if registered.
	 *
	 * A "database" parameter can be specified in order to
	 * automatically select a certain database within that
	 * connection.
	 *
	 * Possible parameters: server, database, strict.
	 *
	 * @see p
	 *
	 * @param     array $p Parameters
	 * @return resource    Found server link identifier
	 */
	public static function get_conn($p = null)
	{
		p($p, array(
			'server'   => null,
			'database' => null,
			'strict'   => true
		));
		
		if(empty($p['server']))
		{
			$p['server'] = self::get_default_server();
		}
		if(self::conn_registered($p['server']))
		{
			$conn = self::$connections[$p['server']];
			if(!empty($p['database']))
			{
				$conn->select_db($p['database'], $p['strict']);
			}
			return $conn;
		}
		return null;
	}
	
	/**
	 * Database select.
	 *
	 * Selects the database for a certain server connection if
	 * specified, or for the default one otherwise.
	 *
	 * Possible parameters: server, database, strict.
	 * Required parameters: database.
	 *
	 * @see p
	 *
	 * @param  array $p Parameters
	 * @return  bool    Set result
	 */
	public static function select_db($p = null)
	{
		p($p, array(
			'server'   => null,
			'database' => REQUIRED_PARAM,
			'strict'   => true
		));
		
		if($conn = self::get_conn($p))
		{
			return $conn->get_db($p['database']); 
		}
		return false;
	}
	
	/**
	 * `count` query type operation.
	 *
	 * Possible parameters: server, database, query, strict.
	 * Required parameters: query.
	 *
	 * @see p
	 * @see MySQLConnection#count
	 *
	 * @param     array $p Parameters
	 * @return bool|int    Number of rows found
	 */
	public static function count($p = null)
	{
		p($p, array(
			'server'   => null,
			'database' => null,
			'query'    => REQUIRED_PARAM,
			'strict'   => true
		));
		
		if($conn = self::get_conn($p))
		{
			return $conn->count($p['query'], $p['strict']);
		}
		return false;
	}
	
	/**
	 * `get` query type operation.
	 *
	 * Possible parameters: server, database, query, result_type, strict.
	 * Required parameters: query.
	 *
	 * @see p
	 * @see MySQLConnection#get
	 *
	 * @param       array $p Parameters
	 * @return bool|array    Row found
	 */
	public static function get($p = null)
	{
		p($p, array(
			'server'      => null,
			'database'    => null,
			'query'       => REQUIRED_PARAM,
			'result_type' => MYSQL_ASSOC,
			'strict'      => true
		));
		
		if($conn = self::get_conn($p))
		{
			return $conn->get($p['query'], $p['result_type'], $p['strict']);
		}
		return false;
	}
	
	/**
	 * `multi get` query type operation.
	 *
	 * Possible parameters: server, database, query, result_type, strict.
	 * Required parameters: query.
	 *
	 * @see p
	 * @see MySQLConnection#multi_get
	 *
	 * @param       array $p Parameters
	 * @return bool|array    Rows found
	 */
	public static function multi_get($p = null)
	{
		p($p, array(
			'server'      => null,
			'database'    => null,
			'query'       => REQUIRED_PARAM,
			'result_type' => MYSQL_ASSOC,
			'strict'      => true
		));
		
		if($conn = self::get_conn($p))
		{
			return $conn->multi_get($p['query'], $p['result_type'], $p['strict']);
		}
		return false;
	}
	
	/**
	 * `set` query type operation.
	 *
	 * Possible parameters: server, database, query, strict.
	 * Required parameters: query.
	 *
	 * @see p
	 * @see MySQLConnection#set
	 *
	 * @param  array $p Parameters
	 * @return  bool    Operation result
	 */
	public static function set($p = null)
	{
		p($p, array(
			'server'   => null,
			'database' => null,
			'query'    => REQUIRED_PARAM,
			'strict'   => true
		));
		
		if($conn = self::get_conn($p))
		{
			return $conn->set($p['query'], $p['strict']);
		}
		return false;
	}
	
	public static function start_transaction($disable_autocommit = true)
	{
		if ($disable_autocommit)
		{
			self::set(array('query' => "SET AUTOCOMMIT = 0"));
		}
		
		return self::set(array('query' => "START TRANSACTION"));
	}
	
	public static function commit($enable_autocommit = true)
	{
		$return = self::set(array('query' => "COMMIT"));
		
		if ($enable_autocommit)
		{
			self::set(array('query' => "SET AUTOCOMMIT = 1"));
		}
		
		return $return;
	}
		
	public static function rollback($enable_autocommit = true)
	{
		$return = self::set(array('query' => "ROLLBACK"));
		
		if ($enable_autocommit)
		{
			self::set(array('query' => "SET AUTOCOMMIT = 1"));
		}
		
		return $return;
	}
	
			/**
	 * SELECT query generation.
	 *
	 * Possible parameters: table, where, order, order_mode, offset, limit, join.
	 * Required parameters: table.
	 *
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $p Parameters
	 * @return string    Query string
	 */
	public static function generate_select($p = null)
	{
		p($p, array(
			'table'      => REQUIRED_PARAM,
			'where'      => null,
			'order'      => null,
			'order_mode' => ASC,
			'offset'     => null,
			'limit'      => null,
			'join'       => null
		));
		
		$query = 'SELECT * FROM `' . $p['table'] . '`';
		
		$query .= self::generate_join_clause($p['join']);
		
		$query .= self::generate_where_clause($p['where']);
		$query .= self::generate_order_clause($p['table'], $p['order'], $p['order_mode']);
		$query .= self::generate_limit_clause($p['offset'], $p['limit']);
		
		return $query;
	}
	
	/**
	 * Aggregate query generation.
	 *
	 * Possible parameters: table, where, order, order_mode, offset, limit, join.
	 * Required parameters: table.
	 *
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $p Parameters
	 * @return string    Query string
	 */
	public static function generate_aggregate($p = null)
	{
		p($p, array(
			'table'      => REQUIRED_PARAM,
			'operation'  => REQUIRED_PARAM,
			'field'      => REQUIRED_PARAM,
			'where'      => null,
			'order'      => null,
			'order_mode' => ASC,
			'offset'     => null,
			'limit'      => null,
			'join'       => null
		));
		
		$query = 'SELECT ' . $p['operation'] . '(' . $p['field'] . ') AS count FROM `' . $p['table'] . '`';
		
		$query .= self::generate_join_clause($p['join']);
		
		$query .= self::generate_where_clause($p['where']);
		$query .= self::generate_order_clause($p['table'], $p['order'], $p['order_mode']);
		$query .= self::generate_limit_clause($p['offset'], $p['limit']);
		
		return $query;
	}
	
	/**
	 * INSERT query generation.
	 *
	 * Possible parameters: table, data.
	 * Required parameters: table, data.
	 *
	 * @see p
	 * @see MySQLController#generate_set_clause
	 *
	 * @param   array $p Parameters
	 * @return string    Query string
	 */
	public static function generate_insert($p = null)
	{
		p($p, array(
			'table' => REQUIRED_PARAM,
			'data'  => REQUIRED_PARAM,
		));
		
		$query = sprintf('INSERT INTO `%s`', self::escape($p['table']));
		
		$query .= self::generate_set_clause($p['data']);
		
		return $query;
	}
	
	/**
	 * UPDATE query generation.
	 *
	 * Possible parameters: table, data, where, order, order_mode, offset, limit.
	 * Required parameters: table, data.
	 *
	 * @see p
	 * @see MySQLController#generate_set_clause
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $p Parameters
	 * @return string    Query string
	 */
	public static function generate_update($p = null)
	{
		p($p, array(
			'table'      => REQUIRED_PARAM,
			'data'       => REQUIRED_PARAM,
			'where'      => null,
			'order'      => null,
			'order_mode' => ASC,
			'offset'     => null,
			'limit'      => null
		));
		
		$query = sprintf('UPDATE `%s`', self::escape($p['table']));
		
		$query .= self::generate_set_clause($p['data']);
		$query .= self::generate_where_clause($p['where']);
		$query .= self::generate_order_clause($p['table'], $p['order'], $p['order_mode']);
		$query .= self::generate_limit_clause($p['offset'], $p['limit']);
		
		return $query;
	}
	
	/**
	 * DELETE query generation.
	 *
	 * Possible parameters: table, where, order, order_mode, offset, limit.
	 * Required parameters: table.
	 *
	 * @see p
	 * @see MySQLController#generate_where_clause
	 * @see MySQLController#generate_order_clause
	 * @see MySQLController#generate_limit_clause
	 *
	 * @param   array $p Parameters
	 * @return string    Query string
	 */
	public static function generate_delete($p = null)
	{
		p($p, array(
			'table'      => REQUIRED_PARAM,
			'where'      => null,
			'order'      => null,
			'order_mode' => ASC,
			'offset'     => null,
			'limit'      => null
		));
		
		$query = sprintf('DELETE FROM `%s`', self::escape($p['table']));
		
		$query .= self::generate_where_clause($p['where']);
		$query .= self::generate_order_clause($p['table'], $p['order'], $p['order_mode']);
		$query .= self::generate_limit_clause($p['offset'], $p['limit']);
		
		return $query;
	}
	
	/**
	 * JOIN query part generation.
	 *
	 * @param   array $tables Table names to be joined
	 * @return string         Query part string
	 */
	public static function generate_join_clause(array $tables = null)
	{
		if(!empty($tables))
		{
			$relations = array();
			foreach($tables as $k => $t)
			{
				if(!is_array($t))
				{
					continue;
				}
				$relations[$k] = sprintf('LEFT JOIN `%s` ON ( `%s`.`%s` = `%s`.`%s` )',
					self::escape($t['joined_table']),
					self::escape($t['parent_table']),
					self::escape($t['parent_key']),
					self::escape($t['child_table']),
					self::escape($t['child_key'])
				);
			}
			if(count($relations))
			{
				return sprintf(' %s', implode($relations, ' '));
			}
		}
		return '';
	}
	
	/**
	 * SET query part generation.
	 *
	 * @param   array $data Data to be set, associative array
	 * @return string       Query part string
	 */
	public static function generate_set_clause($data)
	{
		$fields = array();
		foreach($data as $k => $v)
		{
			if ($v === null)
			{
				$v = 'NULL';
			}
			$format = !in_array($v, self::$magic_values) ? '`%s` = "%s"' : '`%s` = %s';
			$fields[] = sprintf($format, self::escape($k), self::escape($v));
		}
		return sprintf(' SET %s', implode($fields, ', '));
	}
	
	/**
	 * WHERE query part generation.
	 *
	 * @param   array $where Conditions
	 * @return string        Query part string
	 */
	public static function generate_where_clause($where = null)
	{
		if(!empty($where))
		{
			$where_text = $where;
			if(is_array($where))
			{
				$where_text = $where[0];
				for($i = 1; $i < count($where); $i++)
				{
					$pattern = sprintf('$%d', $i);
					$replacement = sprintf('"%s"', self::escape($where[$i]));
					$where_text = str_replace($pattern, $replacement, $where_text);
				}
			}
			return sprintf(' WHERE ( %s )', $where_text);
		}
		return '';
	}
	
	/**
	 * ORDER BY query part generation.
	 *
	 * @param  string $table      Table name
	 * @param  string $order      Order field name
	 * @param  string $order_mode Order type, ASC or DESC
	 * @return string             Query part string
	 */
	public static function generate_order_clause($table, $order = null, $order_mode = null)
	{
		return !is_null($order) ? sprintf(' ORDER BY %s %s',
			self::escape($order),
			self::escape($order_mode)
		) : '';
	}
	
	/**
	 * LIMIT query part generation.
	 *
	 * @param     int $offset Offset
	 * @param     int $limit  Row count
	 * @return string         Query part string
	 */
	public static function generate_limit_clause($offset = null, $limit = null)
	{
		return !is_null($limit) ? sprintf(' LIMIT %s%s',
			!is_null($offset) ? self::escape($offset) . ', ' : '',
			self::escape($limit)
		) : '';
	}
	
	/**
	 * Escape helper for input data.
	 *
	 * @param  string $string Unescaped string
	 * @return string         Safe, escaped string
	 */
	public static function escape($string)
	{
		if(!self::conn_registered())
		{
			throw new Error(500, 'No MySQL connection');
		}
		$string = mysql_real_escape_string($string);
		return $string;
	}
}

if(!class_exists('SQL'))
{
	class_alias('MySQLController', 'SQL');
}

if(!defined('ASC'))
{
	/**
	 * `Ascending` alias constant.
	 *
	 * @package FinallyPHP.sql
	 */
	define('ASC', 'ASC');
}

if(!defined('DESC'))
{
	/**
	 * `Descending` alias constant.
	 *
	 * @package FinallyPHP.sql
	 */
	define('DESC', 'DESC');
}
