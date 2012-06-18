<?php

abstract class LinkedAccount
{
	protected $_src_id;
	protected $_src_state;
	protected $_src_account;
	
	static public function builder($source)
	{
		if (isset($source->account->paypalAccount))
		{
			return new PaypalLinkedAccount($source);
		}
		
		if (isset($source->account->bankAccount))
		{
			return new BankLinkedAccount($source);
		}
		
		if (isset($source->account->cardAccount))
		{
			return new CardLinkedAccount($source);
		}
		
		return null;
	}
	
	public function __construct($source)
	{
		$this->_src_id = $source->id->bancBoxId;
		$this->_src_state = $source->externalAccountStatus;
	}
	
	public function __get($name)
	{
		$getter = "get_$name";
		
		if(method_exists($this, $getter))
		{
			return $this->$getter();
		}
		
		return '-';
	}
	
	abstract public function get_name();
	abstract public function get_institution();
	abstract public function get_type();
	abstract public function get_number();
	
	public function get_bancbox_id()
	{
		return $this->_src_id;
	}
}