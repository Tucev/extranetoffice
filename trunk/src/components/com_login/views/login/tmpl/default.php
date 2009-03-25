<?php
/**
 * @version 	$Id: default.php 29 2009-01-28 15:14:15Z luis.montero $
 * @package		ExtranetOffice
 * @subpackage 	com_login
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Load jQuery validation behaviour for forms
html::validate('form_login');
html::validate('form_openid');
html::validate('form_forgot');
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


<div class="loginbox"> 

<form id="form_login" action="index.php" method="post">
<table class="table_login">
	<tr>
		<td>
			<label for="username" class="label_small">username:</label><br />
			<input class="input_big required" type="text" name="username" id="username" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="password" class="label_small">password:</label><br />
			<input class="input_big required" type="password" name="password" id="password" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<button type="submit">Log in</button> 
			<input type="checkbox" name="remember" /> Remember me
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_user" />
<input type="hidden" name="task" value="login" />
</form>

<br />
<hr />

<h3><a class="toggle" id="openid" href="#">Have an OpenID?</a></h3>
<div>

<form id="form_openid" action="#" method="post">
<table class="table_login">
	<tr>
		<td><strong></strong> Sign in below:</td>
	</tr>
	<tr>
		<td>
			<input class="input_openid required" type="text" name="openid_url" id="openid_url" size="30" maxlength="50" value="ie: http://username.myopenid.com" /> 
			
		</td>
	</tr>
	<tr>
		<td><button>Log in</button></td>
	</tr>
</table>
</form>

</div>


<h3><a class="toggle" id="forgot" href="#">Forgot your password?</a></h3>
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
			<button type="submit">Send password to email address</button> 
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_user" />
<input type="hidden" name="task" value="reset_password" />
</form>

</div>

</div><!-- close .loginbox -->