<?php

class MySoapHeader extends SoapHeader
{
	public function __construct($namespace, $name, $data = null, $mustunderstand = null, $actor = null)
	{
		$return = null;
		
		print_r($this);
		
		if ($actor === null)
		{
			$return = parent::__construct($namespace, $name, $data, $mustunderstand);
		}
		else
		{
			$return = parent::__construct($namespace, $name, $data, $mustunderstand, $actor);
		}
		
		
		print_r($this);
		print_r($return);
		die('f');
		
		return $return;
	}
}