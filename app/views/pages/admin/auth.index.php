<div class="main-bg">
	<div id="content" class="min-height">
		<div class="page-header">
			<h2>Sign into the Getting Started App administration.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				Manage users and confirm payments transacted in the Getting Started App.
			</p>
			<div class="login-wrap">
				<form class="ajax" action="<?= $this->url('login', 'auth') ?>" method="post">
					<div class="form-row">
						<fieldset>
							<label for="username">Username</label>
							<input type="text" name="username" value="enter username..." data-default="enter username..." id="username" />
						</fieldset>
						<fieldset>
							<label for="username">Password</label>
							<input type="password" name="password" value="" id="password" />
						</fieldset>
						<fieldset class="no-label">
							<button type="submit" class="btn btn-primary">
								Sign In
							</button>
						</fieldset>
					</div>
				</form>
			</div><!-- end login-wrap -->
			<div class="forgot-pass">
				<strong>Forgot your password?</strong>
			</div>
			<div class="reset-wrap">
				<form class="ajax" action="<?= $this->url('recover_password', 'auth') ?>" method="post">
					<div class="form-row">
						<span class="help-block paddingB10">Enter your username and we will send you an email.</span>
						<fieldset>
							<label for="enter-username">Username</label>
							<input type="text" name="email" value="enter username..." data-default="enter username..." >
						</fieldset>
						<fieldset class="no-label">
							<button type="submit" class="btn btn-secondary btn-cancel">
								Start Reset
							</button>
						</fieldset>
					</div>
				</form>
			</div><!-- end reset-wrap -->
		</div><!-- page-content -->
	</div><!-- end content -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->