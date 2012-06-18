<div class="main-bg authenticated">
	<div id="content">
		<div class="page-header">
			<h2>Welcome to your Getting Started App account.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<p class="n-message">
				
			</p>
		</div><!-- page-content -->
	</div><!-- end content -->
	<div class="authenticated-wrap">
		<div class="inside">
			<div class="tab-wrap">
				<? $this->partial('user_menu') ?>
				<div class="tab-content">
					<div class="tab-pane active" id="manage-account">
						<div class="manage-my-account">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="heading">Manage Account</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="first-col"><h4>Change my password</h4>
										<p>
											Please provide a new password
										</p>
										<form class="ajax" action="<?= $this->url('change_password', 'auth') ?>">
											<div class="form-row">
												<fieldset>
													<label for="new-password">New Password</label>
													<input type="password" name="password" class="input-medium" value="" />
												</fieldset>
												<fieldset>
													<label for="confirm-new-password">New Password</label>
													<input type="password" name="confirm_password" class="input-medium" value="" />
												</fieldset>
												<fieldset class="no-label">
													<button class="btn">
														Reset Password
													</button>
												</fieldset>
											</div>
										</form></td>
									</tr>
									<tr>
										<td class="first-col">
											<h4>External Linked Account</h4>
											<p>
												Add, edit or remove linked external accounts.
											</p>
											<table class="table">
												<thead>
													<tr class="first">
														<th>Financial Institution</th>
														<th>Type</th>
														<th>Account #</th>
														<th>Routing #</th>
														<th>Expiration</th>
														<th>Holder's name</th>
														<th>&nbsp;</th>
													</tr>
												</thead>
												<tbody>
												<? foreach ($accounts as $account): ?>
													<tr>
														<td><?= $this->html_encode($account->institution) ?></td>
														<td><?= $this->html_encode($account->type) ?></td>
														<td><?= $this->html_encode($account->number) ?></td>
														<td><?= $this->html_encode($account->routing_number) ?></td>
														<td><?= $this->html_encode($account->expiration_date) ?></td>
														<td><?= $this->html_encode($account->holder) ?></td>
														<td><a class="ajax" href="<?= $this->url('unlink_account', 'user') ?>?id=<?= $account->bancbox_id ?>">remove this</a></td>
													</tr>
												<? endforeach ?>
													<tr>
														<td class="last-col" colspan="7">
															<a class="btn pull-right marginT10 modal-iframe" href="<?= $this->url('link_account_form', 'user') ?>">
																Link New External Account
															</a>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td class="first-col">
											<h4>Account Termination</h4>
											<table>
												<tr>
													<td>
														<p>Any outstanding balance in your Getting Started App account will be returned to your linked bank account via ACH.</p>
													</td>
													<td class="width185 last-col">
														<a href="<?= $this->url('terminate_account', 'user')?>" class="btn btn-cancel ajax" data-confirm="Are you sure you want to terminate your account? This action cannot be undone!">Terminate account</a>
													</td>
												</tr>
											</table>
										</td>
									</tr>
							</table>
						</div><!-- end track-payments -->
					</div><!-- end tab-pane -->
				</div>
			</div><!-- end tab-wrap -->
		</div><!-- end inside -->
	</div><!-- end authenticated-wrap -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->