<?php

class PaymentController extends FrontendController
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
		
//		if (!$this->user->is_connected)
//		{
//			$this->user->init_client();
//		}
//		
//		if (!$this->user->is_connected)
//		{
//			throw new Error(500, 'Cannot init client account. Try again later or contact support.');
//		}
	}
	
	public function destructor()
	{
		parent::destructor();
	}
	
	public function index()
	{
		$this->goto_page('schedule');
	}
	
	public function schedule()
	{
		$accounts = array();
		$payments = Payment::get(array(
			'where' => array('user_id = $1 AND state = $2', $this->user->id, Payment::STATE_NEW)
		));
		
		$amount = 0.35;
		$date = '3000-01-01';
		foreach ($payments as $p)
		{
			$amount += $p->amount + 0.35;
			if ($date > $p->date)
			{
				$date = $p->date;
			}
		}
		
		$this->assign('accounts', $this->user->linked_accounts);
		$this->assign('payments', $payments);
		$this->assign('amount', $amount);
		$this->assign('date', $date);
		$this->assign('submited', \request\get('submited', false));
		
		$this->add_script(conf::get('URL') . '/js/payments.js');
	}
	
	public function add_bill()
	{
		$payee_name = \request\post('payee_name');
		$amount = (float) \request\post('amount');
		$account_number = \request\post('account_number');
		$date = DateTime::createFromFormat('m/d/Y', \request\post('date'));
		
		$errors = array();
		
		if (!$payee_name || !($payee = Payee::find(array('name = $1', $payee_name))))
		{
			$errors[] = 'Invalid Payee name';
		}
		
		if ($amount <= 0)
		{
			$errors[] = 'Invalid amount';
		}
		
		if (!$account_number)
		{
			$errors[] = 'Invalid account number';
		}
		
		if (!$date || $date->getTimestamp() <= mktime(23, 59, 59))
		{
			$errors[] = 'Invalid date';
		}
		
		if (!$errors)
		{
			try
			{
				$req = new stdClass();
				$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
				$req->payeeName = $payee_name;
				$req->accountNumber = $account_number;
				
				$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
				$result = $client->searchBancBoxPayees($req);
				
				if($result->status == 1 && isset($result->bancBoxPayees) && $result->bancBoxPayees)
				{
					$payees = \util\to_array($result->bancBoxPayees);
					$payee = $payees[0];
					
					$payment = new Payment();
					$payment->user_id = $this->user->id;
					$payment->bb_payee_id = $payee->id;
					$payment->payee_name = $payee->payeeName;
					$payment->amount = $amount;
					$payment->account_number = $account_number;
					$payment->date = $date->format('Y-m-d');
					$payment->save();
				}
				else
				{
					$error_title = 'Your account number is incorrect.';
					$errors = "You can select $payee_name as the biller you are paying, however, your account number $account_number does not match. Please confirm your account number and biller name before continuing with Add Payment.";
				}
			}
			catch (Exception $e)
			{
				$errors = $e->getMessage();
			}
		}
		
		if ($errors)
		{
			new Notice(400, array(
				'title' => isset($error_title) ? $error_title : null,
				'errors' => $errors
			), \url\internal('schedule'));
		}
		else
		{
			new Notice(200, array(
				'action' => 'reload'
			), \url\internal('schedule'));
		}
	}
	
	public function remove_bill()
	{
		$id = (int) \request\get('id');
		
		if (!$id || !($payment = Payment::find(array('id = $1 AND state = $2', $id, Payment::STATE_NEW))))
		{
			throw new Error(404);
		}
		
		if ($payment->user_id != $this->user->id)
		{
			throw new Error(403);
		}
		
		$payment->destroy();
		
		new Notice(200, array(
			'action' => 'reload'
		), \url\internal('schedule'));
	}
	
	public function submit()
	{
		$account_id = \request\post('account_id');
		
		$payments = Payment::get(array(
			'where' => array('user_id = $1 AND state = $2', $this->user->id, Payment::STATE_NEW)
		));
		
		if (!$account_id || !$payments)
		{
			throw new Error(400);
		}
		
		$account = null;
		
		foreach ($this->user->linked_accounts as $a)
		{
			if ($a->bancbox_id == $account_id)
			{
				$account = $a;
				continue;
			}
		}
		
		if (!$account)
		{
			throw new Error(400);
		}
		
		$amount = 0.35;
		$date = '3000-01-01';
		foreach ($payments as $p)
		{
			$amount += $p->amount + 0.35;
			if ($date > $p->date)
			{
				$date = $p->date;
			}
		}
		
		$debit = new Debit();
		$debit->user_id = $this->user->id;
		$debit->bb_account_id = $account_id;
		$debit->amount = $amount;
		$debit->date = $date;
		$debit->save();
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		
		$req = new stdClass();
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		if (get_class($account) == 'CardLinkedAccount')
		{
			$req->method = array('creditcard' => 'creditcard');
		}
		else
		{
			$req->method = array('ach' => 'ach');
		}
		
		$req->source = array(
			'linkedExternalAccountId' => array('bancBoxId' => $account_id)
		);
		$req->destinationAccount = array('bancBoxId' => $this->user->bb_account_id);
		$req->items = array(
			'referenceId' => 'd' . $debit->id,
			'amount' => $amount,
			'memo' => 'debit',
			'scheduled' => array(
				'scheduleDate' => $date
			)
		);
		$result = $client->collectFunds($req);
		
		if ($result->status != 1)
		{
			$debit->destroy();
			
			throw new Error(500, $result->errors->message);
		}
		
		$debit->bb_id = $result->idStatuses->id->bancBoxId;
		$debit->state = Debit::STATE_SCHEDULED;
		$debit->save();
		
		foreach ($payments as $payment)
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->method = array('billpay' => 'billpay');
			$req->sourceAccount = array('bancBoxId' => $this->user->account->id->bancBoxId);
			$req->destination = array('bancBoxPayeeId' => $payment->bb_payee_id);
			$req->payeeAccountNumber = $payment->account_number;
			$req->items = array(
				'referenceId' => 'p' . $payment->id,
				'amount' => $payment->amount,
				'memo' => 'bill',
				'scheduled' => array(
					'scheduleDate' => $date
				)
			);
			$result = $client->sendFunds($req);
			
			if ($result->status == 1)
			{
				$payment->state = Payment::STATE_SCHEDULED;
				$payment->debit_id = $debit->id;
				$payment->bb_id = $result->sendFundsItemIds->id->bancBoxId;
				$payment->save();
			}
		}
		
		new Notice(200, array(
			'action' => 'redirect'
		), \url\internal('schedule', 'payment', null, 'html') . '?submited=true');
	}
	
	public function payees()
	{
		$payees = Payee::get();
		$payees2 = array();
		
		foreach ($payees as $p)
		{
		//	$payees2[] = array(
		//		'id' => $p->id,
		//		'name' => $p->name
		//	);
			$payees2[] = $p->name;
		}
		
		$this->assign('payees', $payees2);
	}
	
	public function track()
	{
		//check if payments changed their status
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		$schedules = array();
		
		try
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->clientId = array('bancBoxId' => $this->user->bb_client_id);
			
			$result = $client->getSchedules($req);
			
			if($result->status == 1 && isset($result->schedules))
			{
				$schedules = \util\to_array($result->schedules);
			}
		}
		catch (Exception $e)
		{
			
		}
		
		foreach ($schedules as $s)
		{
			if ($s->id->subscriberReferenceId)
			{
				echo substr($s->id->subscriberReferenceId, 1) . '__';
				if (preg_match('/^d([0-9]+)$/', $s->id->subscriberReferenceId))
				{
					if ($debit = Debit::find(array('id = $1', substr($s->id->subscriberReferenceId, 1))))
					{
						$state = $debit->state;
						switch ($s->status)
						{
							case 'SCHEDULED':
								$state = Debit::STATE_SCHEDULED;
								break;
							case 'PENDING':
							case 'IN_PROCESS':
								$state = Debit::STATE_PENDING;
								break;
							case 'COMPLETED':
								$state = Debit::STATE_COMPLETED;
								break;
							case 'FAILED':
								$state = Debit::STATE_FAILED;
								break;
							case 'CANCELLED':
								$state = Debit::STATE_CANCELED;
								break;
						}
						
						if ($debit->state != $state)
						{
							$debit->state = $state;
							$debit->save();
						}
					}
				}
				elseif (preg_match('/^p([0-9]+)$/', $s->id->subscriberReferenceId))
				{
					if ($payment = Payment::find(array('id = $1', substr($s->id->subscriberReferenceId, 1))))
					{
						$state = $payment->state;
						switch ($s->status)
						{
							case 'SCHEDULED':
								$state = Payment::STATE_SCHEDULED;
								break;
							case 'PENDING':
							case 'IN_PROCESS':
								$state = Payment::STATE_PENDING;
								break;
							case 'COMPLETED':
								$state = Payment::STATE_COMPLETED;
								break;
							case 'FAILED':
								$state = Payment::STATE_FAILED;
								break;
							case 'CANCELLED':
								$state = Payment::STATE_CANCELED;
								break;
						}
						
						if ($payment->state != $state)
						{
							$payment->state = $state;
							$payment->save();
						}
					}
				}
			}
		}
		
		$debits = Debit::get(array(
			'where' => array('debits.user_id = $1', $this->user->id),
			'join' => 'payments@debits',
			'order' => 'debits.created_at',
			'order_mode' => 'DESC'
		));
		$this->assign('debits', $debits);
	}
	
	public function cancel_confirm()
	{
		$id = (int) \request\get('id');
		
		if (!$id || !($debit = Debit::find(array('id = $1', $id))))
		{
			throw new Error(404);
		}
		
		if ($debit->user_id != $this->user->id)
		{
			throw new Error(403);
		}
		
		$this->assign('debit', $debit);
		
		$this->view_layout = 'frame';
		$this->add_style(conf::get('URL') . '/css/frame.css');
		$this->add_script(conf::get('URL') . '/js/frame.js');
	}
	
	public function cancel()
	{
		$id = (int) \request\get('id');
		
		if (!$id || !($debit = Debit::find(array('id = $1', $id))))
		{
			throw new Error(404);
		}
		
		if ($debit->user_id != $this->user->id)
		{
			throw new Error(403);
		}
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		
		$payments = $debit->get_payments();
		
		foreach ($payments as $payment)
		{
			if ($payment->state == Payment::STATE_NEW)
			{
				$payment->state = Payment::STATE_CANCELED;
				$payment->save();
				continue;
			}
			
			try
			{
				$req = new stdClass();
				$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
				$req->scheduleIds = array('subscriberReferenceId' => 'p' . $payment->id);
				
				$result = $client->cancelSchedules($req);
				
				if($result->status == 1)
				{
					$payment->state = Payment::STATE_CANCELED;
					$payment->save();
				}
			}
			catch (Exception $e)
			{
				
			}
		}
		
		if ($debit->state == Debit::STATE_NEW)
		{
			$debit->state = Debit::STATE_CANCELED;
			$debit->save();
		}
		else
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->scheduleIds = array('subscriberReferenceId' => 'd' . $debit->id);
			
			$result = $client->cancelSchedules($req);
			
			if($result->status == 1)
			{
				$debit->state = Debit::STATE_CANCELED;
				$debit->save();
			}
		}
		
		new Notice(200, array(
				'action' => 'reload_parent-close'
		), \url\internal('track'));
	}
};