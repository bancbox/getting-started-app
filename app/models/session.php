<?php

class Session extends SQLModel
{
	protected static $classname;
	protected static $table = 'sessions';
	private static $_instance = null;
	private static $_cookie_key = 'SESSION_ID';
	private $_session_data = null;
	
	private static function start()
	{
		if (rand(1, 1000) == 100)
		{
			self::cleanup();
		}
		
		if (self::$_instance !== null)
		{
			return false;
		}
		
		if ($cookie_key = self::load_active_key())
		{
			self::$_cookie_key = $cookie_key;
		}
		
		$session_id = self::_get_id();
		$session = null;
		
		if (!$session_id || !($session = self::find(array('id = $1', $session_id))))
		{
			$session = new self(array(
				'id' => self::generate_id(),
				'data' => '',
				'expires_at' => time() + conf::get('SESSION_DEFAULT_LIFE')
			));
		}
		
		self::$_instance = $session;
		self::_set_id(self::$_instance->id);
		
		register_shutdown_function(function ()
		{
			try
			{
				if ($session = Session::singleton(false))
				{
					$session->save();
				}
			}
			catch (Exception $e)
			{
				
			}
		});
		
		return true;
	}
	public static function stop()
	{
		self::$_instance = null;
	}
	public static function wipe()
	{
		if (self::$_instance !== null)
		{
			self::$_instance->destroy();
		}
		
		self::$_instance = null;
		
		self::_set_id('', time()-1);
		if (self::load_active_key())
		{
			self::save_active_key('', time()-1);
		}
	}
	
	public static function load_active_key()
	{
		if (isset($_COOKIE) && isset($_COOKIE['ACTIVE_COOKIE_KEY']))
		{
			return $_COOKIE['ACTIVE_COOKIE_KEY'];
		}
		
		return null;
	}
	
	public static function save_active_key($key, $expires = null)
	{
		if ($expires === null)
		{
			$expires = time() + conf::get('SESSION_DEFAULT_LIFE');
		}
	
		setcookie('ACTIVE_COOKIE_KEY', $key, $expires, '/');
		$_COOKIE['ACTIVE_COOKIE_KEY'] = $key;
	}
	
	private static function _get_id()
	{
		if (self::$_instance !== null)
		{
			return self::$_instance->id;
		}
		else
		{
			if (isset($_COOKIE) && isset($_COOKIE[self::$_cookie_key]))
			{
				return $_COOKIE[self::$_cookie_key];
			}
			
			return null;
		}
	}
	private static function _set_id($id, $expires = null)
	{
		if ($expires === null)
		{
			$expires = time() + conf::get('SESSION_DEFAULT_LIFE');
		}
		
		setcookie(self::$_cookie_key, $id, $expires, '/');
	}
	public static function generate_id()
	{
		do
		{
			$id = md5(microtime(true));
		}
		while (self::find(array('id = $1', $id)));
		
		return $id;
	}
	
	public static function singleton($start = false)
	{
		if (self::$_instance !== null)
		{
			return self::$_instance;
		}
		
		if ($start)
		{
			self::start();
			
			return self::$_instance;
		}
		
		return false;
	}
	
	public static function get_instance($start = true)
	{
		if (!User::is_bot() && self::singleton($start))
		{
			return self::singleton()->_session_data;
		}
		
		return new SessionData();
	}
	
	public function __construct(array $data = array(), $new = true)
	{
		$return  = parent::__construct($data, $new);
		
		$this->_session_data = new SessionData((array)@unserialize($this->data), false);
		
		return $return;
	}
	public function save()
	{
		$this->data = $this->_session_data->export();
		$this->expires_at = time() + conf::get('SESSION_DEFAULT_LIFE');
		
		return parent::save();
	}
	public function cleanup()
	{
		return self::delete(array('where' => array('expires_at < $1', time())));
	}
}