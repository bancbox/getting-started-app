<!DOCTYPE html>
<!--[if IE 7]>    <html class="ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie" lang="en"> <![endif]-->
<!--[if IE 9 ]>   <html class="ie9 oldie" lang="en"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"><!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<title><?= $this->html_encode($page_title) ?></title>
		<meta name="description" content="<?= $this->html_encode($meta_description) ?>">
		<meta name="author" content="ic">
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
		<![endif]-->
		<? if (isset($csss)): ?>
		<? foreach ($csss as $css): ?>
			<? $this->css($css) ?>
		<? endforeach ?>
		<? endif ?>
		<script>var baseURL = '<?= $url ?>';</script>
		<? if (isset($jss)): ?>
		<? foreach ($jss as $js): ?>
			<? $this->js($js) ?>
		<? endforeach ?>
		<? endif ?>
	</head>
	<body>
		<?= $this->content . "\n" ?>
		<? if (conf::get('ENVIRONMENT') == 'production') $this->partial('google_analytics') ?>
	</body>
</html>