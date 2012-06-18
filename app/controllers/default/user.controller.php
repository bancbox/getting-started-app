<?php

class UserController extends FrontendController
{
	public $user = null;
	
	public function constructor()
	{
		parent::constructor();
		
		if (!User::is_loggedin())
		{
			$this->session->return_url = \url\external();
			$this->goto_page('', 'auth');
		}
		
		$this->user = User::get_current();
		
		if (!$this->user->is_connected)
		{
			$this->user->init_client();
		}
		
		if (!$this->user->is_connected)
		{
			throw new Error(500, 'Cannot init client account. Try again later or contact support.');
		}
	}
	
	public function destructor()
	{
		parent::destructor();
	}
	
	public function index()
	{
		$this->goto_page('manage_account');
	}
	
	public function manage_account()
	{
		$this->assign('accounts', $this->user->linked_accounts);
	}
	
	public function link_account_form()
	{
		$this->view_layout = 'frame';
		$this->add_style(conf::get('URL') . '/css/frame.css');
		$this->add_script(conf::get('URL') . '/js/frame.js');
	}
	
	public function link_account()
	{
		$account_post = (array) \request\post('account');
		$errors = array();
		
		if (!isset($account_post['type'])
		 || !in_array($account_post['type'], array(/*'paypal', */'card', 'bank')))
		{
			$errors[] = 'account type';
		}
		
		$account_type = $account_post['type'];
		
		switch ($account_type)
		{
			case 'paypal':
				$paypal = (array) \request\post('paypal');
				if (!isset($paypal['id']) || !$paypal['id'])
				{
					$errors[] = 'PayPal ID';
					break;
				}
				$account_info = array(
					'id' => $paypal['id']
				);
				break;
			case 'card':
				$card = (array) \request\post('card');
				if (!isset($card['number']) || !\util\is_card_number($card['number']))
				{
					$errors[] = 'Credit card number';
				}
				if (!isset($card['type']) || !in_array($card['type'], array( 'VISA', 'MASTERCARD', 'AMERICANEXPRESS')))
				{
					$errors[] = 'Credit card type';//5555555555554444
				}
				if (!isset($card['expiration_month']) || !isset($card['expiration_year'])
				 || $card['expiration_year'] < 2012 || $card['expiration_year'] > 2020
				 || $card['expiration_month'] < 1 || $card['expiration_month'] > 12
				 || mktime(0, 0, 0, $card['expiration_month'] + 1, 1, $card['expiration_year']) < time())
				{
					$errors[] = 'Expiration date';
				}
				if (!isset($card['holderName']) || !$card['holderName'])
				{
					$errors[] = 'Holder\'s name';
				}
				if (!isset($card['cvv']) || !\util\is_cvv($card['cvv']))
				{
					$errors[] = 'cvv';
				}
				$account_info = array(
					'creditCardAccount' => array(
						'number' => $card['number'],
						'expiryDate' => $card['expiration_month'] . '/' . substr($card['expiration_year'], 2),
						'type' => $card['type'],
						'name' => $card['holderName'],
						'cvv' => $card['cvv'],
						'address' => array(
							'line1' => $card['address'],
							'line2' => '',
							'city' => $card['city'],
							'state' => $card['state'],
							'zipcode' => $card['zipcode'],
						)
					)
				);
				break;
			case 'bank':
				$bank = (array) \request\post('bank');
				if (!isset($bank['routingNumber']) || !\util\is_routing_number($bank['routingNumber'])) //026009593
				{
					$errors[] = 'routing number';
				}
				if (!isset($bank['accountNumber']) || !$bank['accountNumber'])
				{
					$errors[] = 'accountNumber';
				}
				if (!isset($bank['holderName']) || !$bank['holderName'])
				{
					$errors[] = 'holderName';
				}
				$account_info = array(
					'routingNumber' => $bank['routingNumber'],
					'accountNumber' => $bank['accountNumber'],
					'holderName' => $bank['holderName'],
					'bankAccountType' => 'CHECKING'
				);
				break;
		}
		
		if (!$errors)
		{
			try
			{
				$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
				
				$req = new stdClass();
				$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
				$req->clientId = array("subscriberReferenceId" => $this->user->id);
				$req->account = array(
					$account_type . "Account" => $account_info
				);
				
				$result = $client->linkExternalAccount($req);
				
				if ($result->status == 0)
				{
					foreach(\util\to_array($result->errors) as $e)
					{
						$errors[] = array($e->message);
					}
				}
			}
			catch (Exception $e)
			{
				$errors[] = $e->getMessage();
			}
		}
		
		if ($errors)
		{
			new Notice(400, array(
				'errors' => $errors,
			), \url\internal('link_account_form'));
		}
		else
		{
			new Notice(200, array(
				'action' => 'reload_parent-close'
			));
		}
	}
	
	public function unlink_account()
	{
		$id = (int) \request\get('id');
		
		if (!$id)
		{
			throw new Error(400);
		}
		
		$accounts = $this->user->linked_accounts;
		
		$found = false;
		foreach ($accounts as $account)
		{
			if ($account->bancbox_id == $id)
			{
				$found = true;
			}
		}
		
		if (!$found)
		{
			throw new Error(403);
		}
		
		$deleted = false;
		try
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->linkedExternalAccountId = array(
				"bancBoxId" => $id
			);
			
			$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
			$result = $client->deleteLinkedExternalAccount($req);
			
			if ($result->status == 1)
			{
				$deleted = true;
			}
			elseif (isset($result->errors))
			{
				$error = $result->errors->message;
			}
		}
		catch (Exception $e)
		{
			$error = $e->getMessage();
		}
		
		if ($deleted)
		{
			new Notice(200, array(
				'action' => 'reload'
			), \url\internal('manage_accounts', 'user', null, 'html'));
		}
		else
		{
			new Notice(500, array(
				'errors' => isset($error) ? $error : 'Internal error. Try again later.'
			), \url\internal('manage_accounts', 'user', null, 'html'));
		}
	}
	
	public function terminate_account()
	{
		if ($this->user->destroy())
		{
			User::logout();
			new Notice(200, array(
				'action' => 'redirect'
			), \url\internal('', ''));
		}
		else
		{
			new Notice(400, array(
				'errors' => $this->user->get_errors()
			));
		}
	}
};