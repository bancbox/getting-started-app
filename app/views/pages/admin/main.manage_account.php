<div class="main-bg">
	<div id="content">
		<div class="page-header">
			<h2>Getting Started App System Administration</h2>
			<a href="<?= $this->url('', '') ?>" class="btn back-to">back to list</a>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message marginB20">
				<strong> Manage account </strong>
			</p>
			<div class="login-wrap marginB200">
				<form class="ajax" action="<?= $this->url('change_password', 'auth') ?>">
					<div class="form-row">
						<strong>Change my password</strong>
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
								Change password
							</button>
						</fieldset>
					</div>
				</form>
			</div><!-- end login-wrap -->
		</div><!-- page-content -->
	</div><!-- end content -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->
