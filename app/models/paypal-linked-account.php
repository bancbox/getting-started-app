<?php

class PaypalLinkedAccount extends LinkedAccount
{
	public function __construct($source)
	{
		if (!isset($source->account->paypalAccount))
		{
			return null;
		}
		
		parent::__construct($source);
		
		$this->_src_account = $source->account->paypalAccount;
	}
	
	public function get_name()
	{
		return "PayPal " . $this->_src_account->id;
	}
	public function get_institution()
	{
		return 'PayPal';
	}
	public function get_type()
	{
		return '-';
	}
	public function get_number()
	{
		return $this->_src_account->id;
	}
}