<?php

class MainController extends FrontendController
{
	public $view_layout = 'default';
	
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
		$this->page_title = 'Index';
		$this->meta_description = 'sample page';
		
		if (User::is_loggedin())
		{
			$this->goto_page('', 'payment');
		}
	}
}