<?php

class Admin extends SQLModel
{
	protected static $classname;
	protected static $table = 'admins';
	protected static $accessible_fields = array(
		'email',
		'password'
	);
	private static $_current = null;
	private $_raw_password = null;
	
	public function get_display_name()
	{
		return substr(substr($this->email, 0, strpos($this->email, '@')), 0, 40);
	}
	
	protected function validate_email($value)
	{
		return \util\is_email($value)
			&& !self::find(array('email = $2 and id != $1', $this->id, $value));
	}
	protected function validate_password($value)
	{
		return strlen($this->_raw_password) >= 4;
	}
	
	public function set_password($value)
	{
		$this->_raw_password = $value;
		$this->assign('password', sha1($value));
	}
	
	public static function attempt_login($username, $password)
	{
		if ($user = self::find(array('email = $1 AND password = $2', $username, sha1($password))))
		{
			if (self::login($user))
			{
				return $user;
			}
		}
		
		return false;
	}
	public static function login(&$user, $store = true)
	{
		if ($user->active == 0)
		{
			return false;
		}
		
		self::$_current = &$user;
		$user->loggedin_at = date('Y-m-d H:i:s');
		$user->save();
		
		if ($store)
		{
			$session = Session::get_instance();
			$session->admin_id = self::$_current->id;
		}
		
		return true;
	}
	public static function logout()
	{
	//	Session::wipe();
		$session = Session::get_instance();
		$session->admin_id = null;
	}
	public static function get_current()
	{
		if (self::$_current !== null)
		{
			return self::$_current;
		}
		
		$session = Session::get_instance();
		
		if ($session->admin_id && $user = self::find(array('id = $1', $session->admin_id)))
		{
			self::$_current = &$user;
		}
		if (!self::$_current)
		{
			self::$_current = new User(array(
				'id' => 0,
				'username' => 'guest'
			), false);
		}
		
		return self::$_current;
	}
	public static function is_loggedin()
	{
		$user = self::get_current();
		
		return $user->id > 0 ? true : false;
	}
	
	protected function create()
	{
		$this->created_at = date('Y-m-d H:i:s');
		
		return parent::create();
	}
	protected function update()
	{
		$this->modified_at = date('Y-m-d H:i:s');
		
		return parent::update();
	}
}