<?php

class BankLinkedAccount extends LinkedAccount
{
	public function __construct($source)
	{
		if (!isset($source->account->bankAccount))
		{
			return null;
		}
		
		parent::__construct($source);
		
		$this->_src_account = $source->account->bankAccount;
	}
	
	public function get_name()
	{
		return $this->_src_account->holderName . ' ' . $this->_src_account->accountNumber;
	}
	public function get_institution()
	{
		return '-';
	}
	public function get_type()
	{
		return 'Bank account';
	}
	public function get_number()
	{
		return $this->_src_account->accountNumber;
	}
	public function get_routing_number()
	{
		return $this->_src_account->routingNumber;
	}
	public function get_holder()
	{
		return $this->_src_account->holderName;
	}
}