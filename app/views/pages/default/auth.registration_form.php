<div class="main-bg">
	<div id="content">
		<div class="page-header">
			<h2>Complete this simple form to Get Started.</h2>
		</div><!-- end page-header -->
		<div class="page-content">
			<form action="<?= $this->url('create_account', 'auth') ?>" method="post" class="marginL63 registration-form ajax">
				<div class="form-row invitation-code">
					<fieldset>
						<label for="invitation-code">Enter your invite code:</label>
						<input type="text" name="invite_code" value="enter invitation code..." data-default="enter invitation code..." />
						<a href="#invite-code-overlay" data-toggle="modal" class="invite-code">need an invite code?</a>
					</fieldset>
				</div>
				<div class="form-row">
					<h3>Tell us who you are.</h3>
					<fieldset>
						<label for="first-name">First Name</label>
						<input type="text" name="user[first_name]" value="enter first name..." data-default="enter first name..." id="first-name" />
					</fieldset>
					<fieldset>
						<label for="last-name">Last Name</label>
						<input type="text" name="user[last_name]" value="enter last name..." data-default="enter last name..." id="last-name" />
					</fieldset>
					<fieldset>
						<label for="middle-innitial">Middle Initial</label>
						<input type="text" class="input-mini" name="user[middle_name_initial]" value="initial..." data-default="initial..." id="middle-innitial" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<fieldset>
						<label for="email-address">Email Address</label>
						<input type="text" name="user[email]" value="enter email address..." data-default="enter email address..." id="email-address" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<fieldset>
						<label for="home-phone">Home Phone Number</label>
						<input type="text" name="user[home_phone]" value="enter home phone number..." data-default="enter home phone number..." id="home-phone" />
					</fieldset>
					<fieldset>
						<label for="mobile-phone">Mobile Phone Number</label>
						<input type="text" name="user[mobile_phone]" value="enter mobile phone number..." data-default="enter mobile phone number..." id="mobile-phone" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<fieldset>
						<label for="street-address">Line 1 (street address)</label>
						<input type="text" class="input-xxlarge" name="user[street_address]" value="enter address line 1..." data-default="enter address line 1..." id="street-address" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<fieldset>
						<label for="line-two">Line 2 (apartment name, suite number, etc)</label>
						<input type="text" class="input-xxlarge" name="user[street_address_2]" value="enter address line 2..." data-default="enter address line 2..." id="line-two" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<fieldset>
						<label for="city">City</label>
						<input type="text" class="input-medium" name="user[city]" value="enter city..." data-default="enter city..." id="city" />
					</fieldset>
					<fieldset>
						<label for="select-state">State</label>
						<select name="user[state]" class="input-medium" id="select-state">
								<option value="" selected="selected">Select a State</option>
								<option value="AL">Alabama</option>
								<option value="AK">Alaska</option>
								<option value="AZ">Arizona</option>
								<option value="AR">Arkansas</option>
								<option value="CA">California</option>
								<option value="CO">Colorado</option>
								<option value="CT">Connecticut</option>
								<option value="DE">Delaware</option>
								<option value="DC">District Of Columbia</option>
								<option value="FL">Florida</option>
								<option value="GA">Georgia</option>
								<option value="HI">Hawaii</option>
								<option value="ID">Idaho</option>
								<option value="IL">Illinois</option>
								<option value="IN">Indiana</option>
								<option value="IA">Iowa</option>
								<option value="KS">Kansas</option>
								<option value="KY">Kentucky</option>
								<option value="LA">Louisiana</option>
								<option value="ME">Maine</option>
								<option value="MD">Maryland</option>
								<option value="MA">Massachusetts</option>
								<option value="MI">Michigan</option>
								<option value="MN">Minnesota</option>
								<option value="MS">Mississippi</option>
								<option value="MO">Missouri</option>
								<option value="MT">Montana</option>
								<option value="NE">Nebraska</option>
								<option value="NV">Nevada</option>
								<option value="NH">New Hampshire</option>
								<option value="NJ">New Jersey</option>
								<option value="NM">New Mexico</option>
								<option value="NY">New York</option>
								<option value="NC">North Carolina</option>
								<option value="ND">North Dakota</option>
								<option value="OH">Ohio</option>
								<option value="OK">Oklahoma</option>
								<option value="OR">Oregon</option>
								<option value="PA">Pennsylvania</option>
								<option value="RI">Rhode Island</option>
								<option value="SC">South Carolina</option>
								<option value="SD">South Dakota</option>
								<option value="TN">Tennessee</option>
								<option value="TX">Texas</option>
								<option value="UT">Utah</option>
								<option value="VT">Vermont</option>
								<option value="VA">Virginia</option>
								<option value="WA">Washington</option>
								<option value="WV">West Virginia</option>
								<option value="WI">Wisconsin</option>
								<option value="WY">Wyoming</option>
						</select>
					</fieldset>
					<fieldset>
						<label for="zip">Zip</label>
						<input type="text" class="input-medium" name="user[zipcode]" value="enter zip..." data-default="enter zip..." id="zip" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<h3>For your protection, please verify your identity.</h3>
					<fieldset>
						<label for="ssn">Social Security Number</label>
						<input type="text" name="user[ssn]" value="XXX-XX-XXXX" data-default="XXX-XX-XXXX" id="ssn" />
					</fieldset>
					<fieldset>
						<label for="birth-date">Date of Birth</label>
						<input type="text" name="user[birthdate]" value="mm/dd/yyyy" data-default="mm/dd/yyyy" id="birth-date" />
					</fieldset>
				</div><!-- end form-row -->
				<div class="form-row">
					<h3>Please read our terms of service.</h3>
					<div class="terms-content">
						<div class="scroll-pane">
							<div class="scroll-content">
								<p>
									Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tincidunt vestibulum neque eget pharetra. In eget commodo erat. Nam a urna sed turpis ullamcorper euismod. Pellentesque vel tempor mauris. Aenean sodales, mi in laoreet iaculis, nunc tellus euismod massa, sed porta libero arcu pretium ante. Morbi mauris nulla, elementum ut semper ac, blandit sit amet neque. Ut risus dui, consectetur ut facilisis egestas, ultrices non velit. Mauris sem sapien, vulputate sit amet gravida a, varius eget nibh. Nunc placerat libero et lorem porttitor vehicula. Mauris fermentum fermentum venenatis. Phasellus lectus mi, adipiscing vel sollicitudin nec, lobortis at enim.
									Nulla hendrerit tincidunt diam, aliquam posuere nulla feugiat vel. Phasellus consequat quam eget neque dapibus aliquam. Etiam et mauris arcu, eu rutrum odio. Nullam ullamcorper feugiat felis ut aliquet. Donec semper iaculis lorem, quis consectetur magna pretium non. Sed cursus vulputate risus a iaculis. Vestibulum elit quam, posuere a sodales aliquam, fermentum quis quam. Quisque sed risus sapien. Nunc molestie, quam nec pulvinar volutpat, nibh lorem pharetra eros, non dictum leo ante ut enim. Vivamus diam augue, pellentesque at hendrerit non, suscipit non metus. Proin at urna nec lorem convallis iaculis vel non urna. Fusce gravida congue ligula eget elementum. Aenean ac libero erat, euismod convallis nisl. Duis cursus aliquet purus molestie iaculis. Pellentesque augue metus, vestibulum at ullamcorper in, tempor nec ligula.
									Curabitur pellentesque quam non eros dictum dictum suscipit ipsum euismod. Aliquam lorem metus, lobortis tincidunt vehicula sed, venenatis id leo. Suspendisse sit amet justo lorem. Suspendisse non orci mauris. Cras eget iaculis arcu. Aenean vulputate purus sed ante lacinia viverra. Praesent ligula ipsum, dignissim in imperdiet vel, elementum faucibus purus. Sed semper blandit tellus, id pretium nisi vestibulum vitae. Aliquam eget dolor eget mi auctor consequat eget ac turpis. Proin hendrerit lobortis nulla vel rhoncus. Donec semper tincidunt erat, vitae volutpat justo placerat ut.
									Pellentesque sed massa nisl, vitae condimentum odio. Maecenas non sem non mi suscipit lacinia mattis a eros. Vestibulum vitae turpis lorem. Integer sit amet metus purus, suscipit elementum ante. Suspendisse ac urna non orci dignissim pulvinar. Nulla sed facilisis est. In vitae mattis arcu.
									Aliquam sit amet risus dolor, eget vulputate odio. In felis nunc, fermentum non fermentum eget, pulvinar eget metus. Curabitur lacinia nulla et nulla congue porttitor. Proin quis aliquet lorem. Sed luctus accumsan leo, id tempus sem varius ut. Duis vel imperdiet nulla. Donec est erat, pharetra non tempus vitae, tempor eu ligula. Integer quis nulla eros, in adipiscing orci.
								</p>
							</div>
						</div><!-- end scroll-pane -->
					</div><!-- end terms-content -->
				</div><!-- end form-row -->
				<div class="form-row paddingT15">
					<!-- <input type="submit" class="btn" value="Sign me up!" id="sign-me-up"/> -->
					<button class="btn btn-big">
						Sign me up
					</button>
				</div><!-- end form-row -->
			</form>
		</div><!-- end page-contnet -->
	</div><!-- end content -->
	<div class="bottom-content">
		<? $this->partial('bottom_content') ?>
	</div><!-- end bottom-content -->
</div><!-- end main-bg -->
<!-- ******************************************* invite overlay ********************************************* -->
<div class="modal hide" id="invite-code-overlay">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">
			close
		</button>
	</div>
	<div class="modal-body">
		<h2>Get an invite code for the Getting Started App</h2>
		<div class="modal-text">
			<p>
				The Getting Started App is a demonstration application we built for the purpose of showing BancBox developers the basics of integration. We are hosting a live version of this application to allow users to see actual movement of funds in a live enviorment. The application will create a real bank account, do live debits, and make actual bill payments. We are restricting use of the application to developers who are learning about the BancBox, and not to consumers looking for a bill payment solution.
			</p>
		</div>
		<form action="<?= $this->url('request_invite', 'auth') ?>" method="post" class="ajax form-horizontal">
			<div class="form-row">
				<fieldset>
					<span class="help-block"><strong>Please confirm your intentions:</strong></span>
					<label class="radio">
						<input name="intentions" type="radio" value="<?= InviteCode::INTENTION_CONSUMER ?>" />
						I am a consumer looking for a quick bill payment solution.
					</label>
					<label class="radio">
						<input name="intentions" type="radio" value="<?= InviteCode::INTENTION_DEVELOPER ?>" />
						I am a developer who is evaluating the BancBox APIs.
					</label>
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-row">
				<fieldset>
					<span class="help-block"><strong>Please provide your email address so we can send the invite code:</strong></span>
					<input type="text" name="email" data-default="enter email address..." value="enter email address..." />
				</fieldset>
			</div>
			<div class="form-row">
				<fieldset>
					<span class="help-block"> <strong>Please tell us about you or your company:</strong> </span>
					<input type="text" name="name" data-default="enter your / company name..." value="enter your / company name..." />
				</fieldset>
			</div>
			<div class="form-row">
				<fieldset>
					<textarea name="reason" rows="8" cols="40" data-default="Why you are evaluating BancBox">Why you are evaluating BancBox</textarea>
				</fieldset>
				<fieldset class="pull-right paddingT80">
					<button type="submit" class="btn btn-modal">
						Send Invite Code
					</button>
				</fieldset>
			</div>
		</form>
	</div>
	<!-- end modal-body -->
</div>
<!-- end modal -->
<!-- ******************************************* end invite overlay ********************************************* -->
<!-- ******************************************* invite confirmation overlay ********************************************* -->
<div class="modal hide" id="invite-code-confirmation-overlay">
	<div class="modal-header">
		<button class="close" data-dismiss="modal">
			close
		</button>
	</div>
	<div class="modal-body">
		<h2>Get an invite code for the Getting Started App</h2>
		<div class="modal-text">
			<p>
				Thanks for requesting the Getting Started App Invite code. We Will respond to the following email address upon review:
			</p>
			<strong class="sent-email"></strong>
			<p>
				Thanks again. We will notify you shortly.
			</p>
		</div>
		<div class="modal-footer marginT90">
			<a href="#" class="btn btn-modal" data-dismiss="modal">Close this dialog</a>
		</div>
	</div>
	<!-- end modal-body -->
</div>
<!-- end modal -->
<!-- ******************************************* end invite confirmation overlay ********************************************* -->
<script type="text/javascript">
$(document).ready(function () {
	$('.scroll-pane').jScrollPane();
});
</script>