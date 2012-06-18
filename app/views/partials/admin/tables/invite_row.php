<tr>
	<td class="first-row">
		<?= $this->html_encode($item->name) ?>
	</td>
	<td>
		<?= $this->html_encode($item->email) ?>
	</td>
	<td class="evaluation">
		<div class="evaluation-wrap">
			<a href="#"><?= $this->html_encode($this->truncate($item->reason, 50)) ?></a>
			<div class="evaluation-tooltip">
				<p><?= $this->html_encode($item->reason) ?></p>
			</div>
		</div>
	</td>
	<td>
		<?= date_format(new DateTime($item->created_at), 'm/d/Y') ?>
	</td>
	<td class="action-wrap">
		<a href="<?= $this->url('accept_invite', 'main') ?>?id=<?= $item->id ?>" class="btn ajax">Accept</a>
		<a href="<?= $this->url('reject_invite', 'main') ?>?id=<?= $item->id ?>" class="btn ajax btn-cancel">Reject</a>
	</td>
</tr>
