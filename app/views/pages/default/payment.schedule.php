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
					<div class="tab-pane active" id="schedule-payment">
						<div class="add-paypment t-row">
							<form class="ajax" action="<?= $this->url('add_bill', 'payment') ?>" method="post">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="heading" colspan="5">Add a new payment to schedule</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="first-col">
												<label>Who are you paying?</label>
												<input type="text" name="payee_name" value="" id="payee-name-input" />
												<a id="cant-find-payee" href="#" class="p-info">Cannot find?</a>
											</td>
											<td>
												<label> How much?</label>
												<input type="text" name="amount" value="" />
												</td>
												<td><label> What is your account #?</label>
												<input type="text" name="account_number" value="" />
												</td>
												<td>
												<div class="parent">
													<label> When is the payment due? </label>
													<input type="text" name="date" id="show-calendar" value="" />
												</div>
											</td>
											<td class="last-col">
												<button type="submit" class="btn btn-table marginT23">
													Add Payment
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
					<? if ($payments): ?>
						<div class="payment-list t-row">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="heading" colspan="5">Payments in this schedule</th>
									</tr>
								</thead>
								<tbody>
								<? foreach ($payments as $payment): ?>
									<tr>
										<td class="first-col p-name"><strong><?= $this->html_encode($payment->payee_name) ?></strong></td>
										<td><strong>$<?= $payment->amount ?></strong></td>
										<td><strong><?= $this->html_encode($payment->account_number) ?></strong></td>
										<td><strong><?= $payment->date ?></strong></td>
										<td class="last-col"><a class="ajax" href="<?= $this->url('remove_bill', 'payment') ?>?id=<?= $payment->id ?>" data-confirm="Are you sure?">remove this</a></td>
									</tr>
								<? endforeach ?>
								</tbody>
							</table>
						</div>
						<div class="confirm-payments">
							<form class="ajax" action="<?= $this->url('submit', 'payment') ?>" method="post">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th class="heading" colspan="2">Confirm payment details</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="first-col">
											<div class="form-row">
												<fieldset>
													<label>How much?</label>
													<input type="text" name="how_much" class="input-mini" readonly="readonly" value="<?= $amount ?>" />
													<a href="#" class="p-info">why this amount ?</a>
												</fieldset>
												<fieldset>
													<div class="parent">
														<label>When to debit?</label>
														<input type="text" id="when-to-debit" name="when" class="input-medium" readonly="readonly" value="<?= $date ?>" />
														<a href="#" class="p-info">how did we get this date ?</a>
													</div>
												</fieldset>
												<fieldset class="paddingL12">
													<label> What account to debit ?</label>
												<? if ($accounts): ?>
													<select class="width310" name="account_id">
													<? foreach ($accounts as $account): ?>
														<option value="<?= $account->bancbox_id ?>"><?= $this->html_encode($account->name) ?></option>
													<? endforeach ?>
													</select>
												<? else: ?>
													<a class="add-new-account modal-iframe" href="<?= $this->url('link_account_form', 'user') ?>">Add a new Bank Account</a>
												<? endif ?>
												</fieldset>
											</div></td>
											<td class="last-col">
											<? if ($accounts): ?>
												<button type="submit" class="btn btn-table marginT23 pull-right">
													Save and commit this payment
												</button>
											<? endif ?>
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>
					<? else: ?>
						<? if ($submited): ?>
						<h3 class="p-message marginT32">Your payment schedule is saved and ready for processing <a href="<?= $this->url('track', 'payment') ?>">View it here</a></h3>
						<? else: ?>
						<h3 class="p-message marginT32">Build your payment schedule by adding payments from the form above</h3>
						<? endif ?>
					<? endif ?>
					</div>
				</div>
			</div><!-- end tab-wrap -->
		</div><!-- end inside -->
	</div><!-- end authenticated-wrap -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->
<script type="text/javascript">
$(document).ready(function () {
	$('#cant-find-payee').click(function () {
		new alertBox({
			title : 'Cannot find who are you paying?',
			content : 'Please type in the billers name and select for options. For BillPay transactions, we need to locate the payee in the biller directory. Although there are over 10,000 billers in our directory, please be aware that there may be cases where the biller is not present.'
		});
		return false;
	});
});
</script>