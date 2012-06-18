<div class="inside">
	<h1 id="logo"><a href="<?= $this->conf('URL') ?>">BancBox Getting Started App</a></h1>
	<div class="admin-header">
	<? if (Admin::is_loggedin()): ?>
		<strong>Hello <?= $this->html_encode(Admin::get_current()->display_name) ?>!</strong>
		<span>Logged in <em>Addministrator</em></span>
	<? endif ?>
		<nav>
			<ul class="unstyled">
			<? if (Admin::is_loggedin()): ?>
				<li>
					<a class="admin-signout" href="<?= $this->url('logout', 'auth') ?>"> Sign Out </a>
				</li>
				<li class="manage-account">
					<a href="<?= $this->url('manage_account', 'main') ?>">manage account</a>
				</li>
			<? else: ?>
				<li>
					<a class="u-login" href="<?= $this->url('', 'auth') ?>">Admin Login</a>
				</li>
			<? endif ?>
			</ul>
		</nav>
	</div>
</div><!-- end inside -->