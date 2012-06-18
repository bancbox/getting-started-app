welcome <?= $this->html_encode($user->first_name) ?><br /><br />
here are your login credentials:<br />
username: <?= $this->html_encode($user->email) ?><br />
password: <?= $this->html_encode($password) ?><br />
<br />
<br />
<?= $this->url('', 'auth', null, 'html') ?>