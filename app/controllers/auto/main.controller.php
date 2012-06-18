<?php

class MainController extends ProjectController
{
	public $view_layout = null;
	private $accepted_ips = array(
		'127.0.0.1',
		'192.168.0.1',
		'192.168.0.111',
		'192.168.0.112',
		'192.168.0.113'
	);
	
	public function constructor()
	{
		parent::constructor();
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!in_array($ip, $this->accepted_ips))
		{
			throw new Error(403);
		}
		
		set_time_limit(0);
	}
	
	public function destructor()
	{
		parent::destructor();
		
	}
	
	public function update_payees()
	{
		$count = 0;
		$req = new stdClass();
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		$result = $client->searchBancBoxPayees($req);
		
		if($result->status == 1 && isset($result->bancBoxPayees))
		{
			foreach ($result->bancBoxPayees as $p)
			{
				if (!($payee = Payee::find(array('id = $1', $p->id))))
				{
					$payee = new Payee();
				}
				
				$payee->id = $p->id;
				$payee->name = $p->payeeName;
				$payee->street_address = $p->payeeAddress->line1;
				$payee->city = $p->payeeAddress->city;
				$payee->state = $p->payeeAddress->state;
				$payee->zipcode = $p->payeeAddress->zipcode;
				if ($payee->save())
				{
					$count++;
				}
			}
		}
		
		return "inserted/updated $count payees";
	}
}