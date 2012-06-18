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

namespace core
{
	use \conf, \Error;
	
	/**
	 * @package FinallyPHP.core
	 */
	function extension_exists($extension)
	{
		return ($extension == conf::get('DEFAULT_EXTENSION')) || \view\get_content_type($extension);
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function app_exists($app)
	{
		$app_controller_path = conf::get('CONTROLLER_PATH') . "/$app";
		
		return file_exists($app_controller_path);
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function controller_exists($controller, $app = null)
	{
		if(!$app)
		{
			$app = conf::get('APP');
		}
		$controller_path = conf::get('CONTROLLER_PATH') . "/$app/$controller.controller.php";
		
		return file_exists($controller_path);
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function action_exists($action, $controller = null, $app = null)
	{
		if(!$app)
		{
			$app = conf::get('APP');
		}
		if(!$controller)
		{
			$controller = conf::get('CONTROLLER');
		}
		return (
			controller_exists($controller, $app) &&
			(
				method_exists(\controller\classname($controller), $action)
			 || method_exists(\controller\classname($controller), '__call')
			)
		);
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function setup_app($app)
	{
		if(empty($app))
		{
			$app = conf::get('DEFAULT_APP');
		}
		if(!app_exists($app))
		{
			throw new Error(404, sprintf('Invalid app "%s"', $app));
		}
		conf::set('APP', $app);
		
		$app_url = conf::get('URL');
		if($app != conf::get('DEFAULT_APP'))
		{
			$app_url .= "/$app";
		}
		conf::set('APP_URL', $app_url);
		
		\file\require_more(conf::get('APP_LIB_PATH'));
		\file\require_more(conf::get('APP_CONFIG_PATH'));
		
		return $app;
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function setup_controller($controller)
	{
		if(empty($controller))
		{
			$controller = conf::get('DEFAULT_CONTROLLER');
		}
		if(!controller_exists($controller))
		{
			throw new Error(404, sprintf('Invalid controller "%s"', $controller));
		}
		conf::set('CONTROLLER', $controller);
		
		$controller_classname = \controller\classname($controller);
		if(!empty($controller_classname::$default_action))
		{
			conf::set('DEFAULT_ACTION', $controller_classname::$default_action);
		}
		return $controller;
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function setup_action($action)
	{
		if(empty($action))
		{
			$action = conf::get('DEFAULT_ACTION');
		}
		if(!action_exists($action))
		{
			throw new Error(404, sprintf('Invalid action "%s"', $action));
		}
		conf::set('ACTION', $action);
		
		return $action;
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function setup_extension($extension)
	{
		if(empty($extension))
		{
			$extension = conf::get('DEFAULT_EXTENSION');
		}
		if(!extension_exists($extension))
		{
			throw new Error(404, sprintf('Invalid extension "%s"', $extension));
		}
		conf::set('EXTENSION', $extension);
		
		return $extension;
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function load_page($app = null, $controller = null, $action = null, $extension = null, $data = null)
	{
		$extension = setup_extension($extension);
		$app = setup_app($app);
		$controller = setup_controller($controller);
		$action = setup_action($action);
		
		$data = array_merge((array)$_GET, (array)$data);
		
		$view_path = \view\get_page_path($controller, $action, $extension);
		\controller\load(null, $action, $extension, $data, $view_path);
	}
	
	/**
	 * @package FinallyPHP.core
	 */
	function load_error($environment = null, $data = null)
	{
		$extension = conf::get('EXTENSION');
		
		if(!$view_path = \view\get_error_path($environment, $data['code'], $extension))
		{
			$environment = 'standard';
			$view_path = \view\get_error_path($environment, $data['code'], $extension);
		}
		\controller\load('error', $environment, $extension, $data, $view_path);
	}
}

namespace model
{
	/**
	 * @package FinallyPHP.model
	 */
	function exists($name)
	{
		return class_exists($name) && is_subclass_of($name, 'BaseModel');
	}
}

namespace view
{
	use \conf, \View;
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_path(array $choices)
	{
		foreach($choices as $path)
		{
			if(file_exists($path))
			{
				return $path;
			}
		}
		return null;
	}
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_page_path($controller, $action, $request_type)
	{
		$extension = $request_type == conf::get('DEFAULT_EXTENSION') ? '' : ".$request_type";
		
		$project_path = conf::get('VIEW_PAGE_PATH');
		$app_path = conf::get('APP_VIEW_PAGE_PATH');
		
		return get_path(array(
			"$app_path/$controller.$action$extension.php",
			"$app_path/$controller$extension.php",
			"$project_path/$controller.$action$extension.php",
			"$project_path/$controller$extension.php"
		));
	}
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_layout_path($name, $request_type)
	{
		$extension = $request_type == conf::get('DEFAULT_EXTENSION') ? '' : ".$request_type";
		
		$project_path = conf::get('VIEW_LAYOUT_PATH');
		$app_path = conf::get('APP_VIEW_LAYOUT_PATH');
		
		return get_path(array(
			"$app_path/$name$extension.php",
			"$project_path/$name$extension.php"
		));
	}
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_partial_path($name)
	{
		$project_path = conf::get('VIEW_PARTIAL_PATH');
		$app_path = conf::get('APP_VIEW_PARTIAL_PATH');
		
		return get_path(array(
			"$app_path/$name.php",
			"$project_path/$name.php"
		));
	}
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_error_path($environment, $code, $request_type)
	{
		$extension = $request_type == conf::get('DEFAULT_EXTENSION') ? '' : ".$request_type";
		
		$project_path = conf::get('VIEW_ERROR_PATH');
		$app_path = conf::get('APP_VIEW_ERROR_PATH');
		
		return get_path(array(
			"$app_path/$environment.$code$extension.php",
			"$app_path/$environment$extension.php",
			"$project_path/$environment.$code$extension.php",
			"$project_path/$environment$extension.php"
		));
	}
	
	/**
	 * @package FinallyPHP.view
	 */
	function get_content_type($extension)
	{
		$conf_var = strtoupper($extension) . '_CONTENT_TYPE';
		if(conf::is_defined($conf_var))
		{
			return conf::get($conf_var);
		}
		return null;
	}
}

namespace controller
{
	use \conf, \View;
	
	/**
	 * @package FinallyPHP.controller
	 */
	function classname($name = null)
	{
		if($name === null)
		{
			$name = conf::get('CONTROLLER');
		}
		return \str\camelcase_from_hyphen($name) . 'Controller';
	}
	
	/**
	 * @package FinallyPHP.controller
	 */
	function load($name, $action, $extension, $data = null, $view_path = null)
	{
		if($name === null)
		{
			$name = conf::get('CONTROLLER');
		}
		
		$classname = classname($name);
		
		$controller = new $classname();
		$controller->args = $data;
		
		$controller->constructor();
		$response = $controller->$action();
		$controller->destructor();
		
		$view = new View();
		$view->content_type = \view\get_content_type($extension);
		$view->status_code = $controller->status_code;
		
		if($response === null)
		{
			$layout_path = \view\get_layout_path($controller->view_layout, $extension);
			$view->load_path($view_path, $controller->view_data, $layout_path);
		}
		elseif(is_string($response))
		{
			if (strlen($response) <= 20 && $view_path = \view\get_page_path($name, $response, $extension))
			{
				$layout_path = \view\get_layout_path($controller->view_layout, $extension);
				$view->load_path($view_path, $controller->view_data, $layout_path);
			}
			else
			{
				$view->load_content($response);
			}
		}
		exit;
	}
}

namespace log
{
	use \conf;
	
	/**
	 * @package FinallyPHP.log
	 */
	function add($type, $data)
	{
		$config = 'LOG_' . strtoupper($type);
		if(conf::is_defined($config) && !conf::get($config))
		{
			return;
		}
		write_line($type, create_line($data));
	}
	
	/**
	 * @package FinallyPHP.log
	 */
	function create_line($data)
	{
		$line = '';
		foreach($data as $k => $v)
		{
			$line .= sprintf(' -%s %s', $k, $v);
		}
		return sprintf("[%s] %s %s %s\n",
			date('r'),
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['REQUEST_URI'],
			trim($line)
		);
	}
	
	/**
	 * @package FinallyPHP.log
	 */
	function write_line($type, $line)
	{
		$file = conf::get('LOG_PATH') . "/$type";
		$fp = fopen($file, 'a');
		fwrite($fp, $line);
		fclose($fp);
	}
}

namespace file
{
	/**
	 * @package FinallyPHP.file
	 */
	function require_more($folder_path, $recursive = false, $file_pattern = '*.php')
	{
		$files = array();
		$file_names = array();
		
		if(!is_array($file_pattern))
		{
			$file_pattern = array($file_pattern);
		}
		foreach($file_pattern as $file_pattern)
		{
			if($file_pattern && strlen($file_pattern))
			{
				if($matches = glob("$folder_path/$file_pattern"))
				{
					$files = array_merge($files, $matches);
				}
			}
		}
		foreach($files as $file)
		{
			require_once($file);
			array_push($file_names, str_replace("$folder_path/", '', $file));
		}
		if($recursive)
		{
			if($subfolders_array = glob("$folder_path/*", GLOB_ONLYDIR))
			{
				foreach($subfolders_array as $subfolder)
				{
					$returned_files = require_more($subfolder, $recursive, $file_pattern);
					array_merge($file_names, $returned_files);
				}
			}
		}
		return $file_names;
	}
}

namespace url
{
	use \conf;
	
	/**
	 * @package FinallyPHP.url
	 */
	function external($action = null, $controller = null, $app = null, $extension = null, $extra_query = null)
	{
		if($app === null)
		{
			$app = conf::get('APP') == conf::get('DEFAULT_APP') ? '' : conf::get('APP');
		}
		
		if($controller === null)
		{
			$controller = conf::get('CONTROLLER') == conf::get('DEFAULT_CONTROLLER') && !$app ? '' : conf::get('CONTROLLER');
		}
		
		if($action === null)
		{
			$action = conf::get('ACTION') == conf::get('DEFAULT_ACTION') && !$controller ? '' : conf::get('ACTION');
		}
		
		if($action != '')
		{
			$action = "/$action";
		}
		
		if($controller != '')
		{
			$controller = "/$controller";
		}
		
		if($app != '')
		{
			$app = "/$app";
		}
		
		$extra_query = !empty($extra_query) ? "/$extra_query" : '';
		
		if($extension === null)
		{
			$extension = conf::get('EXTENSION');
		}
		elseif($extension === 0)
		{
			$extension = conf::get('DEFAULT_EXTENSION');
		}
		if($extension)
		{
			$extension = ".$extension";
		}
		$extension = (!$controller && !$action && !$extra_query) ? '' : $extension;
		
		return "$app$controller$action$extra_query$extension";
	}
	
	/**
	 * @package FinallyPHP.url
	 */
	function internal($action = null, $controller = null, $app = null, $extension = null, $extra_query = null, $force_secured = false)
	{
		$url = conf::get('URL');
		
		if ($force_secured && strpos($url, 'https') === false)
		{
			$url = str_replace('http', 'https', $url);
		}
		
		return $url . external($action, $controller, $app, $extension, $extra_query, $force_secured);
	}
	
	/**
	 * @package FinallyPHP.url
	 */
	function redirect($url)
	{
		header("Location: $url");
		exit;
	}
}

namespace request
{
	/**
	 * @package FinallyPHP.request
	 */
	function all($name, $default = null)
	{
		return isset($_REQUEST) ? fetch($_REQUEST, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function get($name, $default = null)
	{
		return isset($_GET) ? fetch($_GET, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function post($name, $default = null)
	{
		return isset($_POST) ? fetch($_POST, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function cookie($name, $default = null)
	{
		return isset($_COOKIE) ? fetch($_COOKIE, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function session($name, $default = null)
	{
		return isset($_SESSION) ? fetch($_SESSION, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function file($name, $default = null)
	{
		return isset($_FILES) ? fetch($_FILES, $name, $default) : $default;
	}
	
	/**
	 * @package FinallyPHP.request
	 */
	function fetch($data, $name, $default = null)
	{
		if(!preg_match_all('/([^\[\]]+)/', $name, $matches))
		{
			return $default;
		}
		for($i = 0; $i < count($matches[0]); $i++)
		{
			if(!isset($data[$matches[0][$i]]))
			{
				return $default;
			}
			$data = $data[$matches[0][$i]];
		}
		return $data;
	}
}

namespace str
{
	/**
	 * @package FinallyPHP.str
	 */
	function camelcase_from_underscore($str)
	{
		return preg_replace('/(^|_)([a-z]{1})/e', "strtoupper('$2')", strtolower($str));
	}
	
	/**
	 * @package FinallyPHP.str
	 */
	function camelcase_from_hyphen($str)
	{
		return preg_replace('/(^|-)([a-z]{1})/e', "strtoupper('$2')", strtolower($str));
	}
	
	/**
	 * @package FinallyPHP.str
	 */
	function underscore_from_camelcase($str)
	{
		return strtolower(preg_replace('/([^_])([A-Z]{1})([^A-Z])/', '$1_$2$3', $str));
	}
	
	/**
	 * @package FinallyPHP.str
	 */
	function hyphen_from_camelcase($str)
	{
		return strtolower(preg_replace('/([^-])([A-Z]{1})([^A-Z])/', '$1-$2$3', $str));
	}
	
	/**
	 * @package FinallyPHP.str
	 */
	function filename_from_camelcase($str)
	{
		$str = hyphen_from_camelcase($str);
		return preg_replace('/^(.+)-(controller|model)$/', '$1.$2', $str);
	}
}
