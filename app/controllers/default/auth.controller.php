<?php

class AuthController extends FrontendController
{
	public function constructor()
	{
		parent::constructor();
	}
	
	public function destructor()
	{
		parent::destructor();
		
		if (User::is_loggedin() && conf::get('ACTION') != 'logout')
		{
			$this->goto_page('', 'payment');
		}
	}
	
	public function index()
	{
	}
	
	public function login()
	{
		$username = \request\post('username');
		$password = \request\post('password');
		
		if (User::attempt_login($username, $password))
		{
			if ($this->session->return_url)
			{
				$url = $this->session->return_url;
				$this->session->return_url = null;
			}
			else
			{
				$url = \url\internal('', 'user', null, 'html');
			}
			
			new Notice(200, array(
				'action' => 'redirect'
			), $url);
		}
		else
		{
			throw new Error(401, 'Invalid username and/or password');
		}
	}
	
	public function logout()
	{
		$session = Session::get_instance();
		User::logout();
		
		$this->goto_page('', '');
	}
	
	public function registration_form()
	{
		
	}
	
	public function create_account()
	{
		$invite_code = \request\post('invite_code');
		$user = (array) \request\post('user');
		
		$errors = array();
		
		if (!($invitation = InviteCode::find(array('code = $1 AND used_at IS NULL', $invite_code))))
		{
			$errors[] = 'invite code';
		}
		
		$user = new User($user);
		
		if (!$user->is_valid())
		{
			$errors = array_merge($errors, $user->get_errors());
		}
		
		if (!$errors)
		{
			$user->invite_code_id = $invitation->id;
			$user->save();
			$user->activate();
			
			new Notice(200, array(
				'action' => 'redirect',
			), \url\internal('registration_confirm', 'auth', null, 'html'));
		}
		else
		{
			new Notice(400, array(
				'errors' => $errors
			), \url\internal('registration_form'));
		}
	}
	
	public function registration_confirm()
	{
		
	}
	
	public function recover_password()
	{
		require_once conf::get('LIB_PATH').'/mailer/mail_template.php';
		$email = \request\post('email');
		
		if (!\util\is_email($email))
		{
			throw new Error(400, 'Invalid email');
		}
		
		if ($email && $user = User::find(array('email = $1', $email)))
		{
			$mail = new MailTemplate('recover_password', array(
				'link' => \url\internal('recover_password_step2', 'auth', null, 'html')
				 . '?id=' . $user->id . '&hash=' . $user->password
			));
			$mail->setSubject("recover password");
			$mail->addTo($user->email);
			$mail->send();
			
			$this->session->recover_email = $user->email;
			
			new Notice(200, array(
				'action' => 'redirect'
			), \url\internal('recover_password_step1', 'auth', null, 'html'));
		}
		else
		{
			new Notice(404, array(
				'action' => 'notice',
				'message' => 'The email does not exist in our database!'
			), \url\internal('', 'auth'));
		}
	}
	
	public function recover_password_step1()
	{
		if (!$this->session->recover_email)
		{
			throw new Error(400);
		}
		
		$this->assign('email', $this->session->recover_email);
	}
	
	public function recover_password_step2()
	{
		$id = \request\get('id');
		$hash = \request\get('hash');
		
		if (!$id || !$hash
		 || !($user = User::find(array('id = $1 AND password = $2', $id, $hash))))
		{
			throw new Error(400);
		}
		
		$this->session->recover_email = null;
		$this->session->recover_user_id = $user->id;
	}
	
	public function recover_password_step3()
	{
		if ($this->session->recover_success != true)
		{
			throw new Error(400);
		}
	}
	
	public function change_password()
	{
		if (User::is_loggedin())
		{
			$loggedin = true;
			$user = User::get_current();
		}
		elseif ($this->session->recover_user_id)
		{
			$loggedin = false;
			$user = User::find(array('id = $1', $this->session->recover_user_id));
		}
		
		if (!$user)
		{
			throw new Error(400);
		}
		
		$password = trim(\request\post('password'));
		$confirm_password = trim(\request\post('confirm_password'));
		
		if (strlen($password) <= 3)
		{
			throw new Error(400, 'Password is too short');
		}
		
		if ($password != $confirm_password)
		{
			throw new Error(400, 'Passwords do not match');
		}
		
		$user->password = $password;
		$user->save();
		
		if ($loggedin)
		{
			User::logout();
			
			new Notice(200, array(
				'action' => 'notice-redirect',
				'message' => 'Password changed. Login with the new password.'
			), \url\internal('', 'auth', null, 'html'));
		}
		else
		{
			$this->session->recover_user_id = null;
			$this->session->recover_success = true;
			
			new Notice(200, array(
				'action' => 'redirect'
			), \url\internal('recover_password_step3', 'auth', null, 'html'));
		}
	}
	
	public function request_invite()
	{
		$email = \request\post('email');
		$intentions = \request\post('intentions');
		$name = \request\post('name');
		$reason = \request\post('reason');
		$errors = array();
		
		if (!\util\is_email($email))
		{
			$errors[] = 'email';
		}
		
		if (!in_array($intentions, array(1, 2)))
		{
			$errors[] = 'intentions';
		}
		
		if (strlen($name) <= 1)
		{
			$errors[] = 'name';
		}
		
		if (strlen($reason) <= 1)
		{
			$errors[] = 'reason';
		}
		
		if (!$errors)
		{
			try
			{
				$invitaton = new InviteCode(array(
					'email' => $email,
					'intentions' => $intentions == 1 ? 'developer' : 'consumer',
					'name' => $name,
					'reason' => $reason
				));
				$invitaton->save();
			}
			catch (Exception $e)
			{
				$errors[] = 'Internal error. Try again later.';
			}
		}
		
		if ($errors)
		{
			new Notice(400, array(
				'errors' => $errors,
			), \url\internal('registration_form', 'auth'));
		}
		else
		{
			new Notice(200, array(
				'action' => 'custom_script',
				'script' => "$('#invite-code-confirmation-overlay').find('.sent-email').html('$email'); $('#invite-code-overlay').modal('hide'); $('#invite-code-confirmation-overlay').modal('show');"
			), \url\internal('registration_form', 'auth'));
		}
	}
};