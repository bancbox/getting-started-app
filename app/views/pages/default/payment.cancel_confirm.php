<div class="modal-header">
	<button class="close" data-dismiss="modal">
		close
	</button>
</div>
<div class="modal-body">
	<h2>Cancel Your Payment</h2>
	<div class="modal-text">
		<p class="paddingL10">
			You are cancelling this payment. This action is not reversable. Please confirm.
		</p>
		<strong class="sent-email">Cancel payment of $<?= $debit->amount ?> on <?= $debit->date ?></strong>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn btn-modal pull-left" data-dismiss="modal">Close this and keep payment</a>
		<a href="<?= $this->url('cancel', 'payment') ?>?id=<?= $debit->id ?>" class="btn btn-cancel pull-right ajax">Proceed with cancel request</a>
	</div>
</div>
<!-- end modal-body -->