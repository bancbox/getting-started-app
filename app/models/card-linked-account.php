<?php

class CardLinkedAccount extends LinkedAccount
{
	public function __construct($source)
	{
		if (!isset($source->account->cardAccount)
		 || !isset($source->account->cardAccount->creditCardAccount))
		{
			return null;
		}
		
		parent::__construct($source);
		
		$this->_src_account = $source->account->cardAccount->creditCardAccount;
	}
	
	public function get_name()
	{
		return $this->_src_account->name . ' ' . $this->_src_account->number;
	}
	public function get_institution()
	{
		return $this->_src_account->type;
	}
	public function get_type()
	{
		return 'Credit card';
	}
	public function get_number()
	{
		return $this->_src_account->number;
	}
	public function get_holder()
	{
		return $this->_src_account->name;
	}
	public function get_expiration_date()
	{
		return $this->_src_account->expiryDate;
	}
}