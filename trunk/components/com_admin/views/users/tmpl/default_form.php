<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" name="usersform">

	<fieldset>
		<legend><?php echo text::_( _LANG_ADMIN_USER_DETAILS ); ?></legend>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_USERNAME ); ?></td>
			<td><input type="text" size="40" name="username" value="<?php echo $this->row->username; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_EMAIL ); ?></td>
			<td><input type="text" size="40" name="email" value="<?php echo $this->row->email; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_FIRSTNAME ); ?></td>
			<td><input type="text" size="40" name="firstname" value="<?php echo $this->row->firstname; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_LASTNAME ); ?></td>
			<td><input type="text" size="40" name="lastname" value="<?php echo $this->row->lastname; ?>" /></td>
		</tr>
		</table>
	</fieldset>
	
</form>