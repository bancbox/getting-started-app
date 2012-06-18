<div class="main-bg">
	<div id="content">
		<div class="page-header">
			<h2>Sign into your GettingStartedApp account.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				We have confirmed your email address. Please use the form below to reset your password.
			</p>
			<div class="forgot-pass">
				<strong>Forgot your password?</strong>
			</div>
			<section class="app-actions pattern2">
				<ul class="unstyled">
					<li>
						<div class="image-frame">
							<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-10.png" />
						</div>
						<em>Fill in the reissue form</em>
					</li>
					<li>
						<div class="image-frame">
							<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-11.png" />
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
				<form class="ajax" action="<?= $this->url('change_password', 'auth') ?>">
					<div class="form-row">
						<span class="help-block paddingB10">
							<strong>Please provide new password.</strong>
						</span>
						<fieldset>
							<label for="new-password">New Password</label>
							<input type="password" name="password" value="" />
						</fieldset>
						<fieldset>
							<label for="confirm-new-password">Confirm New Password</label>
							<input type="password" name="confirm_password" value="" />
						</fieldset>
						<fieldset class="no-label">
							<button type="submit" class="btn btn-secondary">
								Reset Password
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