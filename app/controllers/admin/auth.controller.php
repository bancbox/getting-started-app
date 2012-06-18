<?php

class AuthController extends AdminController
{
	public function constructor()
	{
		parent::constructor();
	}
	
	public function destructor()
	{
		parent::destructor();
	}
	
	public function index()
	{
		if (Admin::is_loggedin())
		{
			$this->goto_page('', '');
		}
	}
	
	public function login()
	{
		$username = \request\post('username');
		$password = \request\post('password');
		
		if (!Admin::attempt_login($username, $password))
		{
			new Notice(400, array(
				'action' => 'notice',
				'message' => 'Invalid username and/or password'
			));
		}
		else
		{
			new Notice(200, array(
				'action' => 'redirect'
			), \url\internal('', ''));
		}
	}
	
	public function logout()
	{
		Admin::logout();
		
		$this->goto_page('', '');
	}
	
	public function recover_password()
	{
		require_once conf::get('LIB_PATH').'/mailer/mail_template.php';
		$email = \request\post('email');
		
		if (!\util\is_email($email))
		{
			throw new Error(400, 'Invalid email');
		}
		
		if ($email && $user = Admin::find(array('email = $1', $email)))
		{
			$mail = new MailTemplate('recover_password_admin', array(
				'link' => \url\internal('recover_password_step2', 'auth', null, 'html')
				. '?id=' . $user->id . '&hash=' . $user->password
			));
			$mail->setSubject("recover password");
			$mail->addTo($user->email);
			$mail->send();
			
			$this->session->admin_recover_email = $user->email;
			
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
		if (!$this->session->admin_recover_email)
		{
			throw new Error(400);
		}
		
		$this->assign('email', $this->session->admin_recover_email);
	}
	
	public function recover_password_step2()
	{
		$id = \request\get('id');
		$hash = \request\get('hash');
		
		if (!$id
		 || !($user = Admin::find(array('id = $1 AND password = $2', $id, $hash))))
		{
			throw new Error(400);
		}
		
		$this->session->admin_recover_email = null;
		$this->session->admin_recover_user_id = $user->id;
	}
	
	public function recover_password_step3()
	{
		if ($this->session->admin_recover_success != true)
		{
			throw new Error(400);
		}
	}
	
	public function change_password()
	{
		if (Admin::is_loggedin())
		{
			$loggedin = true;
			$user = Admin::get_current();
		}
		elseif ($this->session->admin_recover_user_id)
		{
			$loggedin = false;
			$user = Admin::find(array('id = $1', $this->session->admin_recover_user_id));
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
			$this->session->admin_recover_user_id = null;
			$this->session->admin_recover_success = true;
			
			new Notice(200, array(
				'action' => 'redirect'
			), \url\internal('recover_password_step3', 'auth', null, 'html'));
		}
	}
};