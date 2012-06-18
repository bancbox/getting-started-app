<?php

require_once conf::get('LIB_PATH').'/mailer/mail.php';

/**
 * Mailer class
 * @author loadrunner
 * @version Oct 8, 2010
 */
class MailTemplate extends Mail
{
	protected $template_name = null;
	protected $_body = '';
	private $data = array();
	
	/**
	 * Class Constructor
	 */
	public function __construct($template_name, $data = array(), $bcc = false)
	{
		parent::__construct();
		
		$this->template_name = $template_name;
		$this->data = $data;
		
		$this->setFrom(conf::get('DEFAULT_EMAIL_FROM'));
	}
	
	private function _buildEmailBody(array $data = array())
	{
		$this->_body = View::path_render(conf::get('VIEW_PAGE_PATH').'/emails/'.$this->template_name.'.php', $data, conf::get('VIEW_LAYOUT_PATH').'/email.php');
	}
	
	public function setFrom($email, $name = null)
	{
		parent::setFrom($email, $name);
	}
	public function addTo($address, $name = null) {
		try {
			parent::addTo($address, $name);
		} catch (Exception $e) {
			
		}
	}
	
	public function send(&$transport = null)
	{
		$this->_buildEmailBody($this->data);
		$this->setBody($this->_body, 'text/html');
		
		if (!$this->getTo()) {
			return false;
		}
		
		return parent::send($transport);
	}
}
