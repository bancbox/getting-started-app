<?php

abstract class ProjectController extends BaseController
{
	protected $_scripts = array();
	protected $_styles = array();
	public $page_title = 'BancBox Getting Started App';
	public $meta_description = 'bancbox getting started app';
	
	public function constructor()
	{
		parent::constructor();
		
		$this->init_db();
		$this->init_html_data();
	}
	
	public function destructor()
	{
		$this->assign('jss', $this->_scripts);
		$this->assign('csss', $this->_styles);
		$this->assign('page_title', $this->page_title);
		$this->assign('meta_description', $this->meta_description);
		
		parent::destructor();
	}
	
	protected function init_db()
	{
		$mysql_server = conf::get('MYSQL_SERVER');
		$mysql_username = conf::get('MYSQL_USERNAME');
		$mysql_password = conf::get('MYSQL_PASSWORD');
		$mysql_database = conf::get('MYSQL_DATABASE');
		
		SQLModel::init($mysql_server, $mysql_username, $mysql_password, $mysql_database);
	}
	protected function init_html_data()
	{
		
	}
	
	protected function add_style($url)
	{
		if (!in_array($url, $this->_styles))
		{
			$this->_styles[] = $url;
		}
	}
	protected function add_script($url)
	{
		if (!in_array($url, $this->_scripts))
		{
			$this->_scripts[] = $url;
		}
	}
}
