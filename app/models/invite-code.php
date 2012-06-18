<?php

class InviteCode extends SQLModel
{
	protected static $classname;
	protected static $table = 'invite_codes';
	
	const INTENTION_DEVELOPER = 1;
	const INTENTION_CONSUMER = 2;
	
	const STATE_NEW = 0;
	const STATE_ACCEPTED = 1;
	const STATE_REJECTED = 2;
	
	public function accept()
	{
		$this->state = self::STATE_ACCEPTED;
		$this->save();
		
		require_once(conf::get('LIB_PATH') . '/mailer/mail_template.php');
		
		$mail = new MailTemplate('invitation_code', array(
			'code' => $this->code,
			'url' => \url\internal('registration_form', 'auth', '', 'html')
		));
		$mail->setTo($this->email);
		$mail->setSubject("You are invited to try out the Getting Started App @BancBox");
		
		return $mail->send();
	}
	
	public function reject()
	{
		$this->state = self::STATE_REJECTED;
		$this->save();
		
		require_once(conf::get('LIB_PATH') . '/mailer/mail_template.php');
		
		$mail = new MailTemplate('invitation_code_rejected');
		$mail->setTo($this->email);
		$mail->setSubject("Your invitation for the Getting Started App @BancBox");
		
		return $mail->send();
	}
	
	protected function create()
	{
		$this->id = 'NULL';
		$this->created_at = date('Y-m-d H:i:s');
		
		if (!parent::create())
		{
			return false;
		}
		
		$str1 = $this->id . date('ms').substr(microtime(), 2, 2).rand(1000, 9999);
		$this->code = strtoupper(base_convert($str1, 10, 36));
		
		return $this->save();
	}
}