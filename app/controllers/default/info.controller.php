<?php

class InfoController extends FrontendController
{
	public function constructor()
	{
		parent::constructor();
	}
	
	public function destructor()
	{
		parent::destructor();
	}
	
	public function about()
	{
		$this->page_title = 'About BancBox';
		$this->meta_description = 'about banc box';
	}
};