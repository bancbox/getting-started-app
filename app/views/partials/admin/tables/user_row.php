<tr>
	<td class="first-row">
		<?= $this->html_encode($item->full_name) ?>
	</td>
	<td class="center">
		<?= date_format(new DateTime($item->created_at), 'm/d/Y') ?>
	</td>
	<td class="">
		<?= $this->html_encode($item->email) ?>
	</td>
	<td class="center">
		<?= $item->invite_code->code ?>
	</td>
	<td class="center">
		<?= $item->loggedin_at ? date_format(new DateTime($item->loggedin_at), 'm/d/Y') : '-' ?>
	</td>
	<td class="action-wrap">
		<a target="_blank" href="<?= $this->url('login_as') ?>?id=<?= $item->id ?>" class="btn btn-primary">Login As</a>
	</td>
</tr>