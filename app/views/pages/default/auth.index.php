<div class="main-bg">
	<div id="content">
		<div class="page-header">
			<h2>Sign into your GettingStartedApp account.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				You should have received your credentials via email. If do not have login credentials, <a href="<?= $this->url('registration_form', 'auth') ?>">sign up here.</a>
			</p>
			<div class="login-wrap">
				<form id="login-form" class="ajax" action="<?= $this->url('login', 'auth') ?>">
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
			<section class="app-actions pattern2">
				<ul class="unstyled">
					<li>
						<div class="image-frame">
							<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-03.png" />
						</div>
						<em>Fill in the reissue form</em>
					</li>
					<li>
						<div class="image-frame">
							<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-07.png" />
						</div>
						<em>Check your email</em>
					</li>
					<li>
						<div class="image-frame">
							<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-09.png" />
						</div>
						<em>Reset your password</em>
					</li>
				</ul>
			</section>
			<div class="reset-wrap">
				<form class="ajax" action="<?= $this->url('recover_password', 'auth') ?>" method="post">
					<div class="form-row">
						<span class="help-block paddingB10">Enter your username and we will send you an email.</span>
						<fieldset>
							<label for="enter-username">Username</label>
							<input type="text" name="email" value="enter username..." data-default="enter username..." >
						</fieldset>
						<fieldset class="no-label">
							<button type="submit" class="btn btn-secondary">
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
