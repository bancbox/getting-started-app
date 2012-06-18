<? if (!isset($params)) $params = array() ?>
<? if (isset($search) && $search): ?>
<div class="search">
	<form method="get" action="<?= $this->url() ?>">
		<input type="text" name="keywords" value="<? if (isset($params['keywords']) && $params['keywords']): ?><?= $params['keywords'] ?><? endif ?>" />
		<input type="submit" value="Search" />
	</form>
</div>
<? endif ?>
<table class="<? if(isset($class)): ?><?= $class ?><? endif ?><? if (isset($sortable) && $sortable): ?> sortable<? endif ?>">
	<thead>
		<? $this->partial('tables/' . $type . '_header', array('url' => $this->url_add_params($this->url(), $params), 'params' => $params)) ?>
	</thead>
	<tbody>
	<? foreach ($items as $item): ?>
		<? $this->partial('tables/' . $type . '_row', array('item' => $item)) ?>
	<? endforeach ?>
	</tbody>
</table>
<? if (isset($pagination)): ?>
<div class="pagination">
	<?= $this->generate_pagination($this->url_add_params($this->url(), $params), $pagination['total'], $pagination['per_page'], $pagination['offset']) ?>
	<div class="clear"></div>
</div>
<? endif ?>