<div class="main-bg">
	<div id="content" class="min-height">
		<div class="page-header">
			<h2>Sign into the Getting Started App administration.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				We have confirmed your email address. Please use the form below to reset your password.
			</p>
			<div class="forgot-pass">
				<form class="ajax" action="<?= $this->url('change_password', 'auth') ?>">
					<div class="form-row">
						<strong>Forgot your password?</strong>
					</div>
					<div class="form-row">
						<p>
							Please provide a new password.
						</p>
					</div>
					<div class="form-row">
						<fieldset>
							<label for="new-password">New password</label>
							<input type="password" name="password" value="" id="new-password" />
						</fieldset>
						<fieldset>
							<label for="comfirm-password">Confirm new password</label>
							<input type="password" name="confirm_password" value="" id="comfirm-password" />
						</fieldset>
						<fieldset class="no-label">
							<button type="submit" class="btn btn-primary">
								Reset Password
							</button>
						</fieldset>
					</div>
				</form>
			</div>
		</div><!-- page-content -->
	</div><!-- end content -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->