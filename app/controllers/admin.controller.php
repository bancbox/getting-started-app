<?php

abstract class AdminController extends ProjectController
{
	public $view_layout = 'admin';
	public $user = null;
	public $session = null;
	
	public function constructor()
	{
		parent::constructor();
		
		$this->session = Session::get_instance();
		$this->page_title = '';
		
		if (!Admin::is_loggedin() && conf::get('CONTROLLER') != 'auth')
		{
			$this->goto_page('', 'auth');
		}
	}
	
	public function destructor()
	{
		if (!$this->page_title)
		{
			$this->page_title = ucfirst(conf::get('CONTROLLER')) . ' ' . str_replace('_', ' ', conf::get('ACTION'));
		}
		
		parent::destructor();
	}
	
	protected function init_html_data()
	{
		parent::init_html_data();
		
	//	$this->add_style(conf::get('URL') . '/css/jquery.loadmask.css');//TODO: remove this
		$this->add_style(conf::get('URL') . '/css/style.css');
		$this->add_style(conf::get('URL') . '/css/jquery.jscrollpane.css');
		$this->add_style('https://fonts.googleapis.com/css?family=PT+Sans:400,700');
		$this->add_style(conf::get('URL') . '/css/my.css');
		
		$this->add_script(conf::get('URL') . '/js/libs/modernizr-2.5.3-respond-1.1.0.min.js');
		$this->add_script(conf::get('URL') . '/js/cufon.js');
		$this->add_script(conf::get('URL') . '/js/PT_Sans_700.font.js');
		$this->add_script('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		$this->add_script(conf::get('URL') . '/js/libs/bootstrap/bootstrap.min.js');
		$this->add_script(conf::get('URL') . '/js/jquery.mousewheel.js');
		$this->add_script(conf::get('URL') . '/js/jquery.jscrollpane.min.js');
	//	$this->add_script(conf::get('URL') . '/js/jquery.loadmask.js');//TODO: remove this
		$this->add_script(conf::get('URL') . '/js/common.js');
		$this->add_script(conf::get('URL') . '/js/form.js');
		$this->add_script(conf::get('URL') . '/js/admin.js');
	}
};