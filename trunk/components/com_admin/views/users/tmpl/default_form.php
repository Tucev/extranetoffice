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

<script language="javascript" type="text/javascript">
function submitbutton() {
	var form = document.usersform;

	// do field validation
	if (form.username.value == "") {
		alert( "<?php echo text::_( 'Please enter a username.', true ); ?>" );
		return false;
	} 
	else if (form.email.value == "") {
		alert( "<?php echo text::_( 'Please enter a valid e-mail address.', true ); ?>" );
		return false;
	} 
	
	form.submit();
}
</script>

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
		<tr>
			<td><?php echo text::_( _LANG_GROUP ); ?></td>
			<td><?php echo usersHelper::selectGroup($this->row->groupid); ?></td>
		</tr>
		<?php if (!empty($this->row->id)) :?>
		<tr>
			<td><?php echo text::_( _LANG_PASSWORD ); ?></td>
			<td><input type="password" size="40" name="password" value="" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_PASSWORD_VERIFY ); ?></td>
			<td><input type="password" size="40" name="password2" value="" /></td>
		</tr>
		<?php else : ?>
		<tr>
			<td><?php echo text::_( _LANG_PASSWORD ); ?></td>
			<td><?php echo text::_( _LANG_PASSWORD_AUTOGEN_INFO ); ?></td>
		</tr>
		<?php endif; ?>
		</table>
	</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (request::getVar('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo text::_(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
<input type="hidden" name="option" value="com_admin" />
<input type="hidden" name="task" value="save_user" />
<input type="hidden" name="layout" value="" />

</form>