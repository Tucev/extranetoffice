<?php
/**
 * @version 	$Id: default.php 29 2009-01-28 15:14:15Z luis.montero $
 * @package		PHPFrame
 * @subpackage 	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Load jQuery validation behaviour for forms
PHPFrame_HTML::validate('form_login');
PHPFrame_HTML::validate('form_openid');
PHPFrame_HTML::validate('form_forgot');
?>

<script type="text/javascript">  
$(document).ready(function() {
	// toggles
	$('a.toggle').parent().next().hide(); 
	$('a.toggle').click(function(e) {
		var toggle_id = $(this).attr('id');
		$('#'+toggle_id).parent().next().slideToggle('normal');
		return false;
	});

	// clear openid url when we click on the input box
	$('#openid_url').click(function() {
		$(this).attr('value', '');
	});
});
</script>

<form id="form_login" action="index.php" method="post">
<table class="table_login">
	<tr>
		<td>
			<label for="username" class="label_small"><?php echo PHPFrame_Base_String::html(_LANG_USERNAME); ?>:</label><br />
			<input class="input_big required" type="text" name="username" id="username" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="password" class="label_small"><?php echo PHPFrame_Base_String::html(_LANG_PASSWORD); ?>:</label><br />
			<input class="input_big required" type="password" name="password" id="password" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_LOGIN); ?></button> 
			<input type="checkbox" name="remember" /> <?php echo PHPFrame_Base_String::html(_LANG_REMEMBER_ME); ?>
		</td>
	</tr>
</table>
<input type="hidden" name="component" value="com_login" />
<input type="hidden" name="action" value="login" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>
</form>

<br />
<hr />

<h3><a class="toggle" id="openid" href="#"><?php echo PHPFrame_Base_String::html(_LANG_HAVE_AN_OPENID); ?></a></h3>
<div>

<form id="form_openid" action="#" method="post">
<table class="table_login">
	<tr>
		<td><?php echo PHPFrame_Base_String::html(_LANG_SIGN_IN_BELOW); ?>:</td>
	</tr>
	<tr>
		<td>
			<input class="input_openid required" type="text" name="openid_url" id="openid_url" size="30" maxlength="50" value="ie: http://username.myopenid.com" /> 
			
		</td>
	</tr>
	<tr>
		<td><button><?php echo PHPFrame_Base_String::html(_LANG_LOGIN); ?></button></td>
	</tr>
</table>
</form>

</div>


<h3><a class="toggle" id="forgot" href="#"><?php echo PHPFrame_Base_String::html(_LANG_FORGOT_PASS); ?></a></h3>
<div>

<form id="form_forgot" action="index.php" method="post">
<table class="table_login">
	<tr>
		<td>
			<label for="email_forgot" class="label_small">email:</label><br />
			<input class="input_big required" type="text" name="email_forgot" id="email_forgot" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SEND_PASS_TO_EMAIL); ?></button> 
		</td>
	</tr>
</table>
<input type="hidden" name="component" value="com_login" />
<input type="hidden" name="action" value="reset_password" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>
</form>

</div>
