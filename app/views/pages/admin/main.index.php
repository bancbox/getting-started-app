<div class="main-bg">
	<div id="content">
		<div class="page-header">
			<h2>Getting Started App System Administration</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				<strong>Use the "login as" link to view a user's profile and transactions.</strong>
			</p>
			<ul class="admin-list unstyled">
				<li>
				Invite Request
				</li>
				<li>
					<span id="admin-users">Users</span>
				</li>
			</ul>
		</div><!-- page-content -->
	</div><!-- end content -->
	<div class="authenticated-wrap admin-bg">
		<div class="inside">
			<div class="invite-request">
				<? $this->partial('table', array('type' => 'invite', 'items' => $invites)) ?>
			</div>
		</div><!-- end inside -->
	</div><!-- end authenticated-wrap -->
	<div class="authenticated-wrap admin-bg2" id="user-list">
		<div class="inside">
			<div class="invite-request">
				<? $this->partial('table', array('type' => 'user', 'items' => $users)) ?>
				<p class="marginT32">Clicking the Login As allows the system admin to view the user's screens as if they were logged in as that user.</p>
			</div>
		</div><!-- end inside -->
	</div><!-- end authenticated-wrap -->
	
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->
