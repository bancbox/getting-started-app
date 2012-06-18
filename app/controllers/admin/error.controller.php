<?php

class ErrorController extends AdminController
{
	public $view_layout = 'admin';
	
	public function constructor()
	{
		parent::constructor();
		
		$this->status_code = $this->arg('code');
		
		$this->assign('code', $this->arg('code'));
		$this->assign('message', $this->arg('message'));
	}
	
	public function standard()
	{
		$code = $this->arg('code');
		$messages = array(
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error'
		);
		$message = isset($messages[$code]) ? $messages[$code] : 'Error';
		
		$this->assign('message', $message);
		$this->assign('page_title', "$code $message");
	}
	
	public function development()
	{
		$this->view_layout = null;
		
		$this->assign_more(array(
			'file'    => $this->arg('file'),
			'line'    => $this->arg('line'),
			'trace'   => $this->arg('trace')
		));
	}
}
