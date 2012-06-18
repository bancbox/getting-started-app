<ul class="nav nav-tabs">
	<li<? if ($this->conf('ACTION') == 'schedule'): ?> class="active"<? endif ?>>
		<a href="<?= $this->url('schedule', 'payment') ?>">Schedule Payment</a>
	</li>
	<li<? if ($this->conf('ACTION') == 'track'): ?> class="active"<? endif ?>>
		<a href="<?= $this->url('track', 'payment') ?>">Track Payments</a>
	</li>
	<li id="manage-my-account"<? if ($this->conf('ACTION') == 'manage_account'): ?> class="active"<? endif ?>>
		<a href="<?= $this->url('manage_account', 'user') ?>">manage my account</a>
	</li>
</ul>
