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

<form action="index.php" method="post">
<table align="center" class="table_login">
	<tr>
		<td>
			<label for="username" class="label_small">username:</label><br />
			<input class="input_big" type="text" name="username" id="username" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="password" class="label_small">password:</label><br />
			<input class="input_big" type="password" name="password" id="password" size="16" maxlength="50" />
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="remember" /> Remember me
			<br />
			<br />
			<button>Log in</button>
			
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_user" />
<input type="hidden" name="task" value="login" />
</form>