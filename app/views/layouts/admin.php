<!doctype html> <!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
	<!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?= $this->html_encode($page_title) ?></title>
		<meta name="description" content="<?= $this->html_encode($meta_description) ?>">
		<meta name="author" content="Okapi - IC">
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
		<script type="text/javascript">
			Cufon.replace('h1, h2, h1, h3, h4, #top-text p');
		</script>
	</head>
	<body>
		<div id="container">
			<header id="header">
				<? $this->partial('header') ?>
			</header>
			<!-- end header -->
			<div id="main-content">
				<?= $this->content . "\n" ?>
			</div><!-- end main-content -->
			<footer id="footer">
				<? $this->partial('footer') ?>
			</footer><!-- end footer -->
		</div><!-- /container -->
	</body>
</html>