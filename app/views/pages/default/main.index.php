<div id="top-content">
	<div id="bubbles">
		<div class="inside">
			<div id="top-text">
				<h2>Getting Started App shows you all of the basics of BancBox APIs!</h2>
				<p>
					Getting started shows you how to create a stored value account
					for your user, fund that account, and pay multiple billers.
				</p>
				<a href="<?= $this->url('registration_form', 'auth') ?>" class="btn btn-large" id="get-started">Get started</a>
				<small>You can test this app live with real money! Create an account here.</small>
			</div><!-- end top-content -->
			<div id="top-video">
				<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-02.png" />
			</div><!-- end video -->
		</div><!-- end inside -->
	</div><!-- end bubbles -->
</div><!-- end top-content -->
<div id="content">
	<h2>Bill payments made easy! <small>One debit, multiple bill payments, one easy app.</small></h2>
	<section class="app-actions">
		<ul class="unstyled">
			<li>
				<div class="image-frame">
					<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-01.png" />
				</div>
				<em>Create an account</em>
			</li>
			<li>
				<div class="image-frame">
					<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-04.png" />
				</div>
				<em>Schedule your payments</em>
			</li>
			<li>
				<div class="image-frame">
					<img src="<?= $url ?>/images/illustrations/Illustations_bancBoxApp-06.png" />
				</div>
				<em>Relax!</em>
			</li>
		</ul>
	</section><!-- end app-actions -->
	<section class="content-box home-page">
		<h3 class="blue-title">Our users love us!</h3>
		<section class="app-info marginR20">
			<div class="app-frame">
				<img src="<? $url ?>/images/Praveer_Kumar.png" alt="Our users love us" />
			</div>
			<div class="app-desc">
				<p>
					See real funds flow with our Getting Started App.
				</p>
			</div>
		</section><!-- end app-info -->
		<section class="app-info">
			<div class="app-frame">
				<img src="<? $url ?>/images/Bill_Wilson.png" alt="Collect. Store. Send" />
			</div>
			<div class="app-desc">
				<p>
					Source code is available. Download it and see how it works.
				</p>
			</div>
		</section><!-- end app-info -->
	</section><!-- end content-box -->
	<section class="home-desc-text">
		<p>
			GettingStartedApp.bancbox.com is a live application that moves real money. Identification verification, debits, and payments are live in production.
		</p>
	</section>
</div><!-- end content -->
<div class="bottom-content">
	<? $this->partial('bottom_content') ?>
</div><!-- end bottom-content -->
