<?php

class ResponseController extends BaseController
{
	public function constructor()
	{
		parent::constructor();
		
		//$this->status_code = $this->arg('code') === 0 ? 200 : 500;
		$this->status_code = 200;
	}
	
	public function main()
	{
		$this->assign('data', $this->arg('data'));
	}
}
