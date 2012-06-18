<?php

require_once conf::get('LIB_PATH').'/swift/swift_required.php';

class Mail extends Swift_Message {
	protected $_subject = '';
	
	public function setBodyHtml($text)
	{
		$this->setBody($text, 'text/html');
	}
	
	public function setSubject($subject)
	{
		$this->_subject = $subject;
	}
	
	public function send(&$mailer = null)
	{
		parent::setSubject($this->_subject);
		
		if ($mailer === null)
		{
			if (conf::is_defined('SMTP_SERVER'))
			{
				//SMTP
				$transport = Swift_SmtpTransport::newInstance(
					conf::get('SMTP_SERVER'),
					conf::is_defined('SMTP_SERVER_PORT') ? conf::get('SMTP_SERVER_PORT') : 25
				);
				
				if (conf::is_defined('SMTP_SERVER_USERNAME'))
				{
					$transport->setUsername(conf::get('SMTP_SERVER_USERNAME'));
					$transport->setPassword(conf::get('SMTP_SERVER_PASSWORD'));
				}
			}
			else
			{
				//Sendmail
			//	$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
				
				//Mail
				$transport = Swift_MailTransport::newInstance();
			}
			
			//Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
		}
		
		return $mailer->send($this);
	}
}