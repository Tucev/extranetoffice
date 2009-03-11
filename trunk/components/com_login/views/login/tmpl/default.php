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
?>
<script type="text/javascript" src="lib/jquery/plugins/validate/jquery.validate.js"></script>

<script>  
	
	$(document).ready(function() {
		
		//need to finis the regex bit beneath
		
		/*jQuery.validator.addMethod("letterswithbasicpunc", function(value, element) {
			return this.optional(element) || /^[a-z-.,()'\"\s]+$/i.test(value);
		}, "Letters or punctuation only please");
		
		$('#form_login').validate({
			rules: {
		  		username: { require : true, minlength : 6, maxlength : 50, letterswithbasicpunc : true }
			}

		});*/
		
		$('#form_login').validate({
			rules: {
		  		username: { require : true, minlength : 6, maxlength : 50 }
			}

		});
	});
		
</script>


<div class="loginbox"> 

<form id="form_login" action="index.php" method="post">
<table align="center" class="table_login">
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
<br />

<table align="center" class="table_login">
	<tr>
		<td><strong>Have an OpenID?</strong> Sign in below:</td>
	</tr>
	<tr>
		<td>
			<input class="input_big input_openid" type="text" name="password" id="password" size="16" maxlength="50" /> 
			<button>Log in</button>
		</td>
	</tr>
	<tr>
		<td>ie: http://username.myopenid.com</td>
	</tr>
</table>

</div><!-- close .loginbox -->
