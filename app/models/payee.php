<?php

class Payee extends SQLModel
{
	protected static $classname;
	protected static $table = 'payees';
	
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