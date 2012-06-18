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
 * MySQLConnection interface.
 * 
 * @package FinallyPHP.sql
 */
interface IMySQLConnection
{
	public function __construct($server, $username, $password = '', $strict = true);
	public function get_link();
	
	public function get_db($database = null);
	public function select_db($database, $strict = true);
	
	public function count($query, $strict = true);
	public function get($query, $result_type = MYSQL_ASSOC, $strict = true);
	public function multi_get($query, $result_type = MYSQL_ASSOC, $strict = true);
	public function set($query, $strict = true);
}

/**
 * MySQL instance class.
 * 
 * @package FinallyPHP.sql
 */
class MySQLConnection implements IMySQLConnection
{
	/**
	 * MySQL server link identifier.
	 *
	 * @var resource
	 */
	private $link = null;
	/**
	 * Selected database.
	 *
	 * @var string
	 */
	private $database = null;
	
	/**
	 * MySQLConnection constructor.
	 *
	 * Tries to establish a MySQL connection, throws Error on fail.
	 *
	 * @see MySQLConnection#error
	 *
	 * @param string $server   MySQL server
	 * @param string $username MySQL username
	 * @param string $password MySQL password
	 * @param   bool $strict   Strict mode, calls error() on fail, if true
	 */
	public function __construct($server, $username, $password = '', $strict = true)
	{
		if(!function_exists('mysql_connect'))
		{
			throw new Error(500, 'MySQL is not installed.');
		}
		if(!($this->link = mysql_connect($server, $username, $password)) && $strict)
		{
			$this->error();
		}
	}
	
	/**
	 * Server link get.
	 *
	 * @return resource
	 */
	public function get_link()
	{
		return $this->link;
	}
	
	/**
	 * Selected database check or set.
	 *
	 * Checks for a certain selected database if specified
	 * or gets the selected one otherwise.
	 *
	 * @param       string $database Database to compare
	 * @return bool|string           Check result or selected database
	 */
	public function get_db($database = null)
	{
		return $database ? $this->database == $database : $this->database;
	}
	
	/**
	 * Database select.
	 *
	 * @param  string $database Database to select
	 * @param    bool $strict   Strict mode, calls error() on fail, if true
	 * @return   bool           Operation result
	 */
	public function select_db($database, $strict = true)
	{
		if($database == $this->database)
		{
			return true;
		}
		if(mysql_select_db($database, $this->link))
		{
			$this->database = $database;
			return true;
		}
		if($strict)
		{
			$this->error();
		}
		return false;
	}
	
	/**
	 * `count` query type operation.
	 *
	 * @see MySQLConnection#error
	 *
	 * @param    string $query  Query string
	 * @param      bool $strict Strict mode, calls error() on fail, if true
	 * @return bool|int         Number of rows found
	 */
	public function count($query, $strict = true)
	{
		if($result = mysql_query($query, $this->link))
		{
			return mysql_num_rows($result);
		}
		if($strict)
		{
			$this->error();
		}
		return false;
	}
	
	/**
	 * `get` query type operation.
	 *
	 * Example query type: SELECT
	 *
	 * @see MySQLConnection#error
	 *
	 * @param      string $query       Query string
	 * @param      string $result_type Result array type
	 * @param        bool $strict      Strict mode, calls error() on fail, if true
	 * @return bool|array              Row found
	 */
	public function get($query, $result_type = MYSQL_ASSOC, $strict = true)
	{
		if($result = mysql_query($query, $this->link))
		{
			return mysql_num_rows($result) ? mysql_fetch_array($result, $result_type) : null;
		}
		if($strict)
		{
			$this->error();
		}
		return false;
	}
	
	/**
	 * `multi get` query type operation.
	 *
	 * The difference between `multi get` and `get` is that `get`
	 * returns only the first row found, and `multi get` returns
	 * an array of all rows founds.
	 *
	 * Example query type: SELECT
	 *
	 * @see MySQLConnection#error
	 *
	 * @param      string $query       Query string
	 * @param      string $result_type Result array type
	 * @param        bool $strict      Strict mode, calls error() on fail, if true
	 * @return bool|array              Rows found
	 */
	public function multi_get($query, $result_type = MYSQL_ASSOC, $strict = true)
	{
		if($result = mysql_query($query, $this->link))
		{
			$rows = array();
			if(mysql_num_rows($result))
			{
				while($row = mysql_fetch_array($result, $result_type))
				{
					array_push($rows, $row);
				}
			}
			return $rows;
		}
		if($strict)
		{
			$this->error();
		}
		return false;
	}
	
	/**
	 * `set` query type operation.
	 *
	 * Example query types: INSERT, UPDATE, DELETE
	 *
	 * @see MySQLConnection#error
	 *
	 * @param  string $query  Query string
	 * @param    bool $strict Strict mode, calls error() on fail, if true
	 * @return   bool         Operation result
	 */
	public function set($query, $strict = true)
	{
		if($result = mysql_query($query, $this->link))
		{
			//return mysql_affected_rows($this->link);
			return true;
		}
		if($strict)
		{
			$this->error();
		}
		return false;
	}
	
	/**
	 * Error function.
	 * Triggered when an operation fails, in strict mode.
	 *
	 * Throws an error with the mysql_error contained within its description.
	 *
	 * @see Error
	 */
	private function error()
	{
		throw new Error(500, 'Invalid MySQL query: "' . mysql_error() . '"');
	}
}