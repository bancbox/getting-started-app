<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title><?= $code ?> Error</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<? $this->css("$url/css/error.css") ?>
</head>
<body>
	<h1><?= $code ?> Error</h1>
	<div class="message"><?= $message ?> in file <?= $file ?> at line <?= $line ?>.</div>
<? if(count($trace)): ?>
	<ul class="trace">
<? foreach($trace as $k => $v): ?>
		<li>#<?= $k + 1 ?>: function <em><?= $v['call'] ?></em> in file <em><?= $v['file'] ?></em> at line <em><?= $v['line'] ?></em></li>
<? foreach($v['args'] as $k2 => $v2): ?>
			<li class="arg"><em><?= var_export($v2, true) ?></em></li>
<? endforeach; ?>
<? endforeach; ?>
	</ul>
<? endif; ?>
</body>
</html>