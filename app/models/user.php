<?php

class User extends SQLModel
{
	protected static $classname;
	protected static $table = 'users';
	protected static $accessible_fields = array(
		'first_name',
		'last_name',
		'middle_name_initial',
		'email',
		'ssn',
		'birthdate',
		'home_phone',
		'mobile_phone',
		'street_address',
		'street_address_2',
		'city',
		'state',
		'zipcode'
	);
	protected static $_bot_list = array(
		'AdsBot [Google]'			=> 'AdsBot-Google',
		'Alexa [Bot]'				=> 'ia_archiver',
		'Alta Vista [Bot]'			=> 'Scooter/',
		'Ask Jeeves [Bot]'			=> 'Ask Jeeves',
		'Baidu [Spider]'			=> 'Baiduspider+(',
		'Bing [Bot]'				=> 'bingbot/',
		'Exabot [Bot]'				=> 'Exabot/',
		'Facebook [Crawler]'		=> 'facebookexternalhit/',
		'FAST Enterprise [Crawler]'	=> 'FAST Enterprise Crawler',
		'FAST WebCrawler [Crawler]'	=> 'FAST-WebCrawler/',
		'Francis [Bot]'				=> 'http://www.neomo.de/',
		'Gigabot [Bot]'				=> 'Gigabot/',
		'Google Adsense [Bot]'		=> 'Mediapartners-Google',
		'Google Desktop'			=> 'Google Desktop',
		'Google Feedfetcher'		=> 'Feedfetcher-Google',
		'Google [Bot]'				=> 'Googlebot',
		'Heise IT-Markt [Crawler]'	=> 'heise-IT-Markt-Crawler',
		'Heritrix [Crawler]'		=> 'heritrix/1.',
		'IBM Research [Bot]'		=> 'ibm.com/cs/crawler',
		'ICCrawler - ICjobs'		=> 'ICCrawler - ICjobs',
		'ichiro [Crawler]'			=> 'ichiro/',
		'Majestic-12 [Bot]'			=> 'MJ12bot/',
		'Metager [Bot]'				=> 'MetagerBot/',
		'MSN NewsBlogs'				=> 'msnbot-NewsBlogs/',
		'MSN [Bot]'					=> 'msnbot/',
		'MSNbot Media'				=> 'msnbot-media/',
		'NG-Search [Bot]'			=> 'NG-Search/',
		'Nutch [Bot]'				=> 'http://lucene.apache.org/nutch/',
		'Nutch/CVS [Bot]'			=> 'NutchCVS/',
		'OmniExplorer [Bot]'		=> 'OmniExplorer_Bot/',
		'Online link [Validator]'	=> 'online link validator',
		'psbot [Picsearch]'			=> 'psbot/0',
		'Seekport [Bot]'			=> 'Seekbot/',
		'Sensis [Crawler]'			=> 'Sensis Web Crawler',
		'SEO Crawler'				=> 'SEO search Crawler/',
		'Seoma [Crawler]'			=> 'Seoma [SEO Crawler]',
		'SEOSearch [Crawler]'		=> 'SEOsearch/',
		'Snappy [Bot]'				=> 'Snappy/1.1 ( http://www.urltrends.com/ )',
		'Steeler [Crawler]'			=> 'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/',
		'Synoo [Bot]'				=> 'SynooBot/',
		'Telekom [Bot]'				=> 'crawleradmin.t-info@telekom.de',
		'TurnitinBot [Bot]'			=> 'TurnitinBot/',
		'Twitter [Bot]'				=> 'Twitterbot/',
		'Voyager [Bot]'				=> 'voyager/1.0',
		'W3 [Sitesearch]'			=> 'W3 SiteSearch Crawler',
		'W3C [Linkcheck]'			=> 'W3C-checklink/',
		'W3C [Validator]'			=> 'W3C_*Validator',
		'WiseNut [Bot]'				=> 'http://www.WISEnutbot.com',
		'YaCy [Bot]'				=> 'yacybot',
		'Yahoo MMCrawler [Bot]'		=> 'Yahoo-MMCrawler/',
		'Yahoo Slurp [Bot]'			=> 'Yahoo! DE Slurp',
		'Yahoo [Bot]'				=> 'Yahoo! Slurp',
		'YahooSeeker [Bot]'			=> 'YahooSeeker/'
	);
	protected static $_is_bot = null;
	protected static $_current = null;
	protected $_raw_password = null;
//	protected $_raw_password_confirm = null;
	
	public function get_full_name()
	{
		return implode(' ', \util\array_cleanup(array($this->first_name, $this->last_name)));
	}
	public function get_name()
	{
		return $this->full_name;
	}
	public function get_is_connected()
	{
		if ($this->bb_client_id && $this->bb_account_id)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function get_account()
	{
		$account = null;
		
		try
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->clientId = array("bancBoxId" => $this->bb_client_id);
			
			$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
			$result = $client->getClientAccounts($req);
			
			if($result->status == 1 && isset($result->accounts))
			{
				$accounts = \util\to_array($result->accounts);
				if ($accounts)
				{
					$account = $accounts[0];
				}
			}
		}
		catch (Exception $e)
		{
			
		}
		$this->cache('account', $account);
		return $account;
	}
	public function get_linked_accounts()
	{
		$accounts = array();
		
		try
		{
			$req = new stdClass();
			$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
			$req->clientId = array("bancBoxId" => $this->bb_client_id);
			
			$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
			$result = $client->getClientLinkedExternalAccounts($req);
			
			if($result->status == 1 && isset($result->linkedExternalAccounts))
			{
				foreach (\util\to_array($result->linkedExternalAccounts) as $account)
				{
					if ($account->externalAccountStatus != 'DELETED')
					{
						$accounts[] = LinkedAccount::builder($account);
					}
				}
			}
		}
		catch (Exception $e)
		{
			
		}
		$this->cache('linked_accounts', $accounts);
		return $accounts;
	}
	public function set_password($value)
	{
		$this->_raw_password = $value;
		$this->assign('password', sha1($value));
	}
//	public function set_confirm_password($value)
//	{
//		$this->_raw_password_confirm = $value;
//	}
//	public function set_raw_password($value)
//	{
//		$this->assign('password', $value);
//	}
	public function set_birthdate($value)
	{
		$date = DateTime::createFromFormat('m/d/Y', $value);
		$this->assign('birthdate', $date->format('Y-m-d'));
	}
	public function set_mobile_phone($value)
	{
		$this->assign('mobile_phone', strlen(trim($value)) > 0 ? $value : null);
	}
	
	protected function validate_first_name($value)
	{
		return strlen(trim($value)) > 1;
	}
	protected function validate_last_name($value)
	{
		return strlen(trim($value)) > 1;
	}
	protected function validate_email($value)
	{
		return \util\is_email($value)
			&& !self::find(array('email = $2 and id != $1', $this->id, $value));
	}
//	protected function validate_password($value)
//	{
//		return $this->_raw_password == $this->_raw_password_confirm && strlen($this->_raw_password) >= 4;
//	}
	protected function validate_ssn($value)
	{
		return \util\is_ssn($value);
	}
	protected function validate_home_phone($value)
	{
		return \util\is_phone_number($value);
	}
	protected function validate_mobile_phone($value)
	{
		return $value === null || \util\is_phone_number($value);
	}
	protected function validate_birthdate($value)
	{
		if (!$value || !($date = DateTime::createFromFormat('Y-m-d', $value)))
		{
			return false;
		}
		
		$diff = $date->diff(new DateTime("now"));
		
		return $diff->y > 18 && $diff->y < 120;
	}
	protected function validate_zipcode($value)
	{
		return \util\is_zipcode($value);
	}
	protected function validate_city($value)
	{
		return strlen(trim($value)) > 1;
	}
	protected function validate_state($value)
	{
		return preg_match('/^([A-Z]{2})$/', $value);
	}
	protected function validate_street_address($value)
	{
		return strlen(trim($value)) > 1;
	}
	
	public static function attempt_login($email, $password)
	{
		if (!\util\is_email($email) || !$password)
		{
			return false;
		}
		
		if ($user = self::find(array('email = $1 AND password = $2', $email, sha1($password))))
		{
			if (self::login($user))
			{
				return $user;
			}
		}
		
		return false;
	}
	public static function login(&$user, $store = true)
	{
		if ($user->active == 0)
		{
			return false;
		}
		
		self::$_current = &$user;
		$user->loggedin_at = date('Y-m-d H:i:s');
		$user->save();
		
		if ($store)
		{
			$session = Session::get_instance();
			$session->user_id = self::$_current->id;
		}
		
		return true;
	}
	public static function logout()
	{
		//local
		Session::wipe();
	}
	public static function get_current()
	{
		if (self::$_current !== null)
		{
			return self::$_current;
		}
		
		if (self::is_bot())
		{
			self::$_current = new User(array('id' => -1));
		}
		else
		{
			$session = Session::get_instance();
			
			if ($session->user_id)
			{
				if ($user = User::find(array('id = $1 AND active = 1', $session->user_id)))
				{
					self::$_current = &$user;
				}
				else
				{
					$session->user_id = null;
				}
			}
			if (!self::$_current)
			{
				self::$_current = new User(array('id' => 0));
			}
		}
		
		if (self::$_current->id > 0)
		{
			self::$_current->loggedin_at = date('Y-m-d H:i:s');
			self::$_current->save();
		}
		
		return self::$_current;
	}
	public static function is_loggedin()
	{
		$user = self::get_current();
		
		return $user && $user->id > 0 ? true : false;
	}
	public static function is_bot()
	{
		if (self::$_is_bot !== null)
		{
			return self::$_is_bot;
		}
		
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		
		foreach (self::$_bot_list as $key => $val)
		{
			if ($val && preg_match('#' . str_replace('\*', '.*?', preg_quote($val, '#')) . '#i', $browser))
			{
				self::$_is_bot = true;
				return true;
			}
		}
		self::$_is_bot = false;
		return false;
	}
	
	public function activate()
	{
		$this->active = 1;
		$this->password = strtoupper(substr(md5(rand(1, 10000)), 0, 5));
		
		require_once(conf::get('LIB_PATH') . '/mailer/mail_template.php');
		
		$mail = new MailTemplate('user_intro', array(
			'user' => $this,
			'password' => $this->_raw_password
		));
		$mail->setTo($this->email);
		$mail->setSubject("welcome to bancbox");
		$mail->send();
		
		$this->save();
		$this->init_client();
		
		return true;
	}
	public function init_client()
	{
		if ($this->is_connected)
		{
			return true;
		}
		
		try
		{
			$this->_create_bb_client();
			$this->_create_bb_account();
		}
		catch (Exception $e)
		{
			return false;
		}
		
		return true;
	}
	public function terminate_client()
	{
		try
		{
			$this->_close_bb_account();
			$this->_close_bb_client();
		}
		catch (Exception $e)
		{
			$this->add_error($e->getMessage());
			return false;
		}
		
		return true;
	}
	
	private function _create_bb_client()
	{
		if ($this->bb_client_id)
		{
			return true;
		}
		
		$req = new stdClass();
		
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		$req->referenceId = $this->id;
		$req->email = $this->email;
		$req->firstName = $this->first_name;
		$req->lastName = $this->last_name;
		$req->ssn = $this->ssn;
		$req->dob = $this->birthdate;
		$req->address = array(
			"line1" => $this->street_address,
			"line2" => $this->street_address_2,
			"city" => $this->city,
			"state" => $this->state,
			"zipcode" => $this->zipcode
		);
		$req->homePhone = $this->home_phone;
		$req->mobilePhone = $this->mobile_phone;
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		
		$result = $client->createClient($req);
		
		if ($result->status == 1)
		{
			$this->bb_client_id = $result->clientId->bancBoxId;
			$this->save();
			
			return true;
		}
		
		throw new Error(500, "cannot create client");
	}
	private function _create_bb_account()
	{
		if ($this->bb_account_id)
		{
			return true;
		}
		
		$req = new stdClass();
		
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		$req->clientId = array('bancBoxId' => $this->bb_client_id);
		$req->title = "default account";
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		
		$result = $client->openAccount($req);
		
		if ($result->status == 1)
		{
			$this->bb_account_id = $result->account->id->bancBoxId;
			$this->save();
			
			return true;
		}
		
		throw new Error(500, "cannot create account");
	}
	private function _close_bb_client()
	{
		if (!$this->bb_client_id)
		{
			return false;
		}
		
		$req = new stdClass();
		
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		$req->clientId = array(
			'bancBoxId' => $this->bb_client_id
		);
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		
		$result = $client->cancelClient($req);
		
		if ($result->status == 1)
		{
			$this->bb_client_id = null;
			$this->save();
			
			return true;
		}
		
		throw new Error(500, "cannot delete client");
	}
	private function _close_bb_account()
	{	
		if (!$this->bb_account_id)
		{
			return true;
		}
		
		$req = new stdClass();
		
		$req->subscriberId = conf::get('BANCBOX_API_SUBSCRIBER_ID');
		$req->accountId = array(
			'bancBoxId' => $this->bb_account_id
		);
		
		if ($this->account && (1 || $this->account->current_balance != 0))
		{
			if (count($this->linked_accounts) > 0)
			{
				if (get_class($this->linked_accounts[0]) == 'PaypalLinkedAccount')
				{
					$req->withdrawalMethod = 'PAYPAL';
				}
				else
				{
					$req->withdrawalMethod = 'ACH';
				}
				$req->withdrawalAccount = array('linkedAccountId' => $this->linked_accounts[0]->bancbox_id);
			}
			else
			{
				throw new Error(400, 'We need an external account for the refund.');
			}
		}
		
		$client = new MySoapClient(conf::get('BANCBOX_API_WDSL'));
		$result = $client->closeAccount($req);
		
		if ($result->status == 1)
		{
			$this->bb_account_id = null;
			$this->save();
			
			return true;
		}
		
		throw new Error(500, "cannot close account");
	}
	
	protected function create()
	{
		$this->id = 'NULL';
		$this->created_at = date('Y-m-d H:i:s');
		
		if ($invitation = InviteCode::find(array('id = $1', $this->invite_code_id)))
		{
			$invitation->used_at = date('Y-m-d H:i:s');
			$invitation->save();
		}
		
		return parent::create();
	}
	protected function update()
	{
		$this->modified_at = date('Y-m-d H:i:s');
		
		return parent::update();
	}
	
	public function destroy()
	{
		if ($this->terminate_client())
		{
			Debit::delete(array('where' => array('user_id = $1', $this->id)));
			Payment::delete(array('where' => array('user_id = $1', $this->id)));
			
			return parent::destroy();
		}
		else
		{
			return false;
		}
	}
}