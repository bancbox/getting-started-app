<div class="inside">
	<h1 id="logo"><a href="<?= $this->conf('URL') ?>">BancBox Getting Started App</a></h1>
	<nav>
		<ul class="unstyled">
		<? if (User::is_loggedin()): ?>
			<li>
				<a class="u-signout" href="<?= $this->url('logout', 'auth') ?>"> Sign Out </a>
			</li>
		<? else: ?>
			<li>
				<a class="u-login" href="<?= $this->url('', 'auth') ?>">User Login</a>
			</li>
		<? endif ?>
		</ul>
		<? if (User::is_loggedin()): ?>
		<span class="logged-user"> <a href="#">Hello <?= $this->html_encode(User::get_current()->first_name) ?>!</a> </span>
		<? endif ?>
	</nav>
</div><!-- end inside -->