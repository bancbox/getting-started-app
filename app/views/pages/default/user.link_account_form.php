<div class="modal-header">
	<button class="close" data-dismiss="modal">
		close
	</button>
</div>
<div class="modal-body">
	<h2>Link External Account</h2>
	<div class="modal-text">
		<p>
			You need to link an external account in order to initiate payment.
		</p>
	</div>
	<form id="link-account-form" class="ajax form-horizontal" action="<?= $this->url('link_account', 'user') ?>" method="post">
		<div class="form-row">
			<fieldset>
				<label for="account-type">Account type</label>
				<select id="account-type-selector" name="account[type]">
					<option value="0" selected="selected">enter type</option>
					<!-- option value="paypal">Paypal</option -->
					<option value="bank">Bank</option>
					<option value="card">Card</option>
				</select>
			</fieldset>
		</div><!-- end form-row -->
		<div id="paypal-options">
			<div class="form-row">
				<fieldset>
					<label for="account-id">Card number</label>
					<input type="text" name="paypal[id]" data-default="enter account id..." value="enter account id..." id="account-id" />
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-actions paddingT30">
				<fieldset class="pull-right">
					<button type="submit" class="btn btn-modal">
						Link this account
					</button>
				</fieldset>
			</div><!-- end form-actions -->
		</div>
		<div id="card-options">
			<div class="form-row">
				<fieldset>
					<label for="account-number">Card number</label>
					<input type="text" name="card[number]" data-default="enter number..." value="enter number..." id="account-number" />
				</fieldset>
				<fieldset>
					<label for="holder-name-1">Holder's name</label>
					<input type="text" name="card[holderName]" data-default="enter holder's name..." value="enter holder's name..." id="holder-name-1" />
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-row">
				<fieldset>
					<label for="card-type">Holder's name</label>
					<select name="card[type]" id="card-type">
						<option value="">Select credit card type</option>
						<option value="VISA">VISA</option>
						<option value="MASTERCARD">MASTERCARD</option>
						<option value="AMERICANEXPRESS">AMERICANEXPRESS</option>
					</select>
				</fieldset>
				<fieldset>
					<label for="expiration-m">Expiration date</label>
					<select name="card[expiration_year]" class="width85" id="expiration-m">
						<option value="">year</option>
						<option value="2012">2012</option>
						<option value="2013">2013</option>
						<option value="2014">2014</option>
						<option value="2015">2015</option>
						<option value="2016">2016</option>
						<option value="2017">2017</option>
						<option value="2018">2018</option>
						<option value="2019">2019</option>
						<option value="2020">2020</option>
					</select>
				</fieldset>
				<fieldset class="no-label">
					<select name="card[expiration_month]" class="width60">
						<option value="">month</option>
						<option value="01">1</option>
						<option value="02">2</option>
						<option value="03">3</option>
						<option value="04">4</option>
						<option value="05">5</option>
						<option value="06">6</option>
						<option value="07">7</option>
						<option value="08">8</option>
						<option value="09">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</fieldset>
				<fieldset>
					<label for="cvv">CVV</label>
					<input type="text" class="width45" name="card[cvv]" data-default="cvv..." value="cvv..." id="cvv" />
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-row">
				<fieldset>
					<label for="street-address">Address</label>
					<input type="text" name="card[address]" data-default="enter street address..." value="enter street address..." id="street-address" />
				</fieldset>
				<fieldset>
					<label for="zip-code">Zipcode</label>
					<input type="text" name="card[zipcode]" data-default="enter zipcode..." value="enter zipcode..." id="zip-code" />
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-row">
				<fieldset>
					<label for="city">City</label>
					<input type="text" name="card[city]" data-default="enter city..." value="enter city..." id="city" />
				</fieldset>
				<fieldset>
					<label for="state">State</label>
					<select name="card[state]">
						<option value="">select state</option>
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
			</div><!-- end form-row -->
			<div class="form-actions paddingT30">
				<fieldset class="pull-right">
					<button type="submit" class="btn btn-modal">
						Link this account
					</button>
				</fieldset>
			</div><!-- end form-actions -->
		</div>
		<div id="bank-options">
			<div class="form-row">
				<fieldset>
					<label for="account-number">Account number</label>
					<input type="text" name="bank[accountNumber]" data-default="enter account number..." value="enter account number..." id="account-number">
				</fieldset>
				<fieldset>
					<label for="routing-number">Routing number</label>
					<input type="text" name="bank[routingNumber]" data-default="enter routing number..." value="enter routing number..." id="routing-number">
				</fieldset>
			</div><!-- end form-row -->
			<div class="form-row">
				<fieldset>
					<label for="holder-name-1">Holder's name</label>
					<input type="text" name="bank[holderName]" data-default="enter holder's name..." value="enter holder's name..." id="holder-name-1">
				</fieldset>
			</div>
			<div class="form-actions paddingT30">
				<fieldset class="pull-right">
					<button type="submit" class="btn btn-modal">
						Link this account
					</button>
				</fieldset>
			</div><!-- end form-actions -->
		</div>
		
	</form>
</div>
<!-- end modal-body -->
<script type="text/javascript">
$(document).ready(function () {
	$('#account-type-selector').val(0);
	$('#account-type-selector').change(function () {
		switch ($(this).val()) {
			case 'paypal':
				$('#paypal-options').show();
				$('#card-options').hide();
				$('#bank-options').hide();
				break;
			case 'card':
				$('#paypal-options').hide();
				$('#card-options').show();
				$('#bank-options').hide();
				break;
			case 'bank':
				$('#paypal-options').hide();
				$('#card-options').hide();
				$('#bank-options').show();
				break;
			default:
				$('#paypal-options').hide();
				$('#card-options').hide();
				$('#bank-options').hide();
		}
	});
	$('#paypal-options').hide();
	$('#card-options').hide();
	$('#bank-options').hide();
});
</script>