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
					<div class="tab-pane active" id="track-payments">
						<div class="track-payments">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="heading" colspan="7">Your payment schedule</th>
									</tr>
								</thead>
								<tbody>
								<? foreach ($debits as $debit): ?>
									<tr>
										<td class="first-col" valign="top">
											<a href="#">(<?= count($debit->payments) ?>)</a>
										</td>
										<td>
											<span>$<?= number_format($debit->amount, 2) ?></span>
										<? foreach ($debit->payments as $p): ?>
											<small>$<?= number_format($p->amount, 2) ?></small>
										<? endforeach ?>
										</td>
										<td>
											<span><?= $debit->date ?></span>
										<? foreach ($debit->payments as $p): ?>
											<small><?= $p->date ?></small>
										<? endforeach ?>
										</td>
										<td>
											<span>--</span>
										<? foreach ($debit->payments as $p): ?>
											<small><?= $this->html_encode($p->payee_name . ' ' . $p->account_number) ?></small>
										<? endforeach ?>
										</td>
										<td class="empty">
											&nbsp;
										</td>
										<td>
											<span>Status: <?= $debit->state_text ?></span>
										<? foreach ($debit->payments as $p): ?>
											<small>Status: <?= $p->state_text ?></small>
										<? endforeach ?>
										</td>
										<td class="last-col" valign="top">
										<? if (in_array($debit->state, array(Debit::STATE_NEW, Debit::STATE_SCHEDULED))): ?>
											<a href="<?= $this->url('cancel_confirm', 'payment') ?>?id=<?= $debit->id ?>" class="modal-iframe width750 height275">
												cancel
											</a>
										<? endif ?>
										</td>
									</tr>
								<? endforeach ?>
								</tbody>
							</table>
						</div><!-- end track-payments -->
					</div>
				</div>
			</div><!-- end tab-wrap -->
		</div><!-- end inside -->
	</div><!-- end authenticated-wrap -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->