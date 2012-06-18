<?php

class MainController extends AdminController
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
		$this->page_title = 'Admin panel';
		
		$this->assign('users', User::get(array(
			'join' => 'invite_codes.users',
			//'where' => $where
		)));
		$this->assign('invites', InviteCode::get(array(
			'where' => array('state = $1', InviteCode::STATE_NEW)
		)));
	}
	
	public function login_as()
	{
		$id = (int)\request\get('id');
		
		if (!($user = User::find(array('id = $1', $id))))
		{
			throw new Error(404);
		}
		
	//	Session::stop();
	//	Session::save_active_key('CUSTOM_SESSION_ID');
		
		User::login($user);
		
		$this->goto_page('', '', '');
	}
	
	public function accept_invite()
	{
		$id = (int)\request\get('id');
		
		if (!($invitaton = InviteCode::find(array('id = $1', $id))))
		{
			throw new Error(404);
		}
		
		if ($invitaton->state != InviteCode::STATE_NEW)
		{
			throw new Error(400);
		}
		
		$invitaton->accept();
		
		new Notice(200, array(
			'action' => 'notice-reload',
			'message' => 'Invitation code sent.'
		), \url\internal('', ''));
	}
	
	public function reject_invite()
	{
		$id = (int)\request\get('id');
		
		if (!($invitaton = InviteCode::find(array('id = $1', $id))))
		{
			throw new Error(404);
		}
		
		if ($invitaton->state != InviteCode::STATE_NEW)
		{
			throw new Error(400);
		}
		
		$invitaton->reject();
		
		new Notice(200, array(
			'action' => 'notice-reload',
			'message' => 'Rejection email sent.'
		), \url\internal('', ''));
	}
	
	public function manage_account()
	{
		
	}
}
