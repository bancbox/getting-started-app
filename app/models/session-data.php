<?php

class SessionData extends BaseModel
{
	public function add_error($value)
	{
		if (!is_array($this->error_msgs))
		{
			$this->error_msgs = array();
		}
		
		$errors = $this->error_msgs;
		$errors[] = $value;
		$this->error_msgs = $errors;
	}
	public function get_errors($string = false)
	{
		$errors = $this->error_msgs;
		if ($errors)
		{
			$this->error_msgs = null;
			return $string ? implode(', ', (array)$errors) : $errors;
		}
		return false;
	}
	public function has_errors() {
		return (boolean) $this->error_msgs;
	}
	public function get_success_message()
	{
		$msg = $this->success_msg;
		if ($msg)
		{
			$this->success_message = null;
			return $msg;
		}
		return false;
	}
	public function set_success_message($value)
	{
		$this->assign('success_msg', $value);
	}
	
	public function preference($key, $default, $prefix = null, $type = all, $save = true)
	{
		$s_key = ($prefix  ? $prefix . '_' : '') . $key;
		
		$default = $this->$s_key !== null ? $this->$s_key : $default;
		
		switch (strtolower($type))
		{
			case 'get':
				$value = \request\get($key, $default);
				break;
			case 'post':
				$value = \request\post($key, $default);
				break;
			case 'all':
			default:
				$value = \request\all($key, $default);
		}
		
		if ($save)
		{
			$this->$s_key = $value;
		}
		
		return $value;
	}
	public function set_preference($key, $value, $prefix = null)
	{
		$s_key = ($prefix  ? $prefix . '_' : '') . $key;
		
		$this->$s_key = $value;
	}
	
	public function get_post()
	{
		$post = $this->old_post;
		if ($post)
		{
			$this->old_post = null;
			return $post;
		}
		return array();
	}
	public function save_post()
	{
		$this->assign('old_post', $_POST);
	}
	
	public function export()
	{
		return serialize($this->_data);
	}
}