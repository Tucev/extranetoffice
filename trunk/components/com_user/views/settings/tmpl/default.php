<?php
/**
* @package		ExtranetOffice
* @subpackage 	com_user
* @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
* @license		BSD revised. See LICENSE.
* @author 		Luis Montero [e-noise.com]
* @version 		1.0.0
*/

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<script type="text/javascript" charset="utf-8">
	window.addEvent('domready', init);
	function init() {
		myTabs1 = new mootabs('myTabs', {
			width: '98%',
			height: '440px'
		});
	}
</script>

<script language="javascript" type="text/javascript">
function submitbutton( pressbutton ) {
	var form = document.iofficesettingsform;
	var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

	if (pressbutton == 'cancel') {
		form.task.value = 'cancel';
		form.submit();
		return;
	}

	// do field validation
	if (form.name.value == "") {
		alert( "<?php echo text::_( 'Please enter your name.', true );?>" );
	} else if (form.email.value == "") {
		alert( "<?php echo text::_( 'Please enter a valid e-mail address.', true );?>" );
	} else if (((form.password.value != "") || (form.password2.value != "")) && (form.password.value != form.password2.value)){
		alert( "<?php echo text::_( 'REGWARN_VPASS2', true );?>" );
	} else if (r.exec(form.password.value)) {
		alert( "<?php printf( text::_( 'VALID_AZ09', true ), text::_( 'Password', true ), 4 );?>" );
	} else {
		form.submit();
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" name="iofficesettingsform" enctype="multipart/form-data">

<div id="myTabs">
<ul class="mootabs_title">
	<li title="general">General</li>
	<li title="dashboard">Dashboard</li>
	<?php if ($this->config->get('enable_email_client') == '1') : ?>
	<li title="email">Email</li>
	<?php endif; ?>
	
	<?php if ($this->config->get('enable_contacts') == '1') : ?>
	<li title="contacts">Contacts</li>
	<?php endif; ?>
	
</ul>

<div id="general" class="mootabs_panel">
	
<fieldset class="josform">
<legend><?php echo text::_( _LANG_GENERAL_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ioffice_edit">
<tr>
	<td>
		<label for="username">
			<?php echo text::_( 'User Name' ); ?>:
		</label>
	</td>
	<td>
		<span><?php echo $this->user->get('username');?></span>
	</td>
</tr>
<tr>
	<td width="120">
		<label for="name">
			<?php echo text::_( 'Your Name' ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="name" name="name" value="<?php echo $this->user->get('name');?>" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="email">
			<?php echo text::_( 'email' ); ?>:
		</label>
	</td>
	<td>
		<input type="text" id="email" name="email" value="<?php echo $this->user->get('email');?>" size="40" />
	</td>
</tr>
<?php if ($this->user->get('password')) : ?>
<tr>
	<td>
		<label for="password">
			<?php echo text::_( 'Password' ); ?>:
		</label>
	</td>
	<td>
		<input type="password" id="password" name="password" value="" size="40" />
	</td>
</tr>
<tr>
	<td>
		<label for="password2">
			<?php echo text::_( 'Verify Password' ); ?>:
		</label>
	</td>
	<td>
		<input type="password" id="password2" name="password2" size="40" />
	</td>
</tr>
<?php endif; ?>
<tr>
	<td width="30%">
		<label id="photomsg" for="photo">
			<?php echo _LANG_SETTINGS_PHOTO; ?>:
		</label>
	</td>
	<td>
		<?php if (!empty($this->settings->photo)) : ?>
			<img src="<?php echo $this->config->get('upload_dir').'/users/'.$this->settings->photo; ?>" alt="photo" vspace="5" />
			<br />
		<?php endif; ?>
		<input type="file" name="photo" id="photo" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="email_notificationsmsg" for="email_notifications">
			<?php echo _LANG_SETTINGS_EMAIL_NOTIFICATIONS; ?>:
		</label>
	</td>
	<td>
		<select id="email_notifications" name="email_notifications">
			<option value="0" <?php if ($this->settings->email_notifications == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->settings->email_notifications == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="show_emailmsg" for="show_email">
			<?php echo _LANG_SETTINGS_SHOW_EMAIL; ?>:
		</label>
	</td>
	<td>
		<select id="show_email" name="show_email">
			<option value="0" <?php if ($this->settings->show_email == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->settings->show_email == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
</table>
</fieldset>

</div><!-- #general close -->

<div id="dashboard" class="mootabs_panel">
<fieldset class="josform">
<legend><?php echo text::_( _LANG_DASHBOARD_SETTINGS ); ?></legend>

</fieldset>
</div><!-- #contacts close -->

<?php if ($this->config->get('enable_email_client')) : ?>

<div id="email" class="mootabs_panel">
<fieldset class="josform">
<legend><?php echo text::_( _LANG_EMAIL_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ioffice_edit">
<tr>
	<td width="30%">
		<label id="enable_email_clientmsg" for="enable_email_client">
			<?php echo _LANG_SETTINGS_ENABLE_EMAIL_CLIENT; ?>:
		</label>
	</td>
	<td>
		<select id="enable_email_client" name="enable_email_client">
			<option value="0" <?php if ($this->settings->enable_email_client == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->settings->enable_email_client == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="server_typemsg" for="server_type">
			<?php echo _LANG_SETTINGS_SERVER_TYPE; ?>:
		</label>
	</td>
	<td>
		<select id="server_type" name="server_type">
			<option value="IMAP" <?php if ($this->settings->server_type == 'IMAP') echo 'selected'; ?>>IMAP</option>
		</select>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="email_signaturemsg" for="email_signature">
			<?php echo _LANG_SETTINGS_EMAIL_SIGNATURE; ?>:
		</label>
	</td>
	<td>
		<textarea id="email_signature" name="email_signature" rows="4" cols="60"><?php echo $this->settings->email_signature; ?></textarea>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="incoming_servermsg" for="incoming_server">
			<?php echo _LANG_SETTINGS_INCOMING_SERVER; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="incoming_server" name="incoming_server" size="32" maxlength="128" value="<?php echo $this->settings->incoming_server; ?>" />
		<label id="incoming_server_portmsg" for="incoming_server_port">
			<?php echo _LANG_PORT; ?>:
		</label>
		<input type="text" id="incoming_server_port" name="incoming_server_port" size="6" maxlength="6" value="<?php echo $this->settings->incoming_server_port; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="incoming_server_usernamemsg" for="incoming_server_username">
			<?php echo _LANG_SETTINGS_USERNAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="incoming_server_username" name="incoming_server_username" size="32" maxlength="128" value="<?php echo $this->settings->incoming_server_username; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="incoming_server_passwordmsg" for="incoming_server_password">
			<?php echo _LANG_SETTINGS_PASSWORD; ?>:
		</label>
	</td>
	<td>
		<input type="password" id="incoming_server_password" name="incoming_server_password" size="32" maxlength="64" value="<?php echo $this->settings->incoming_server_password; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="from_namemsg" for="from_name">
			<?php echo _LANG_SETTINGS_FROM_NAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="from_name" name="from_name" size="32" maxlength="64" value="<?php echo $this->settings->from_name; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="email_addressmsg" for="email_address">
			<?php echo _LANG_SETTINGS_EMAIL_ADDRESS; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="email_address" name="email_address" size="32" maxlength="128" value="<?php echo $this->settings->email_address; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="outgoing_servermsg" for="outgoing_server">
			<?php echo _LANG_SETTINGS_OUTGOING_SERVER; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="outgoing_server" name="outgoing_server" size="32" maxlength="128" value="<?php echo $this->settings->outgoing_server; ?>" />
		<label id="outgoing_server_portmsg" for="outgoing_server_port">
			<?php echo _LANG_PORT; ?>:
		</label>
		<input type="text" id="outgoing_server_port" name="outgoing_server_port" size="6" maxlength="6" value="<?php echo $this->settings->outgoing_server_port; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="outgoing_server_authmsg" for="outgoing_server_auth">
			<?php echo _LANG_SETTINGS_OUTGOING_SERVER_AUTH; ?>:
		</label>
	</td>
	<td>
		<select id="outgoing_server_auth" name="outgoing_server_auth">
			<option value="0" <?php if ($this->settings->outgoing_server_auth == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->settings->outgoing_server_auth == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="outgoing_server_usernamemsg" for="outgoing_server_username">
			<?php echo _LANG_SETTINGS_OUTGOING_SERVER_USERNAME; ?>:
		</label>
	</td>
	<td>
		<input type="text" id="outgoing_server_username" name="outgoing_server_username" size="32" maxlength="128" value="<?php echo $this->settings->outgoing_server_username; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="outgoing_server_passwordmsg" for="outgoing_server_password">
			<?php echo _LANG_SETTINGS_OUTGOING_SERVER_PASSWORD; ?>:
		</label>
	</td>
	<td>
		<input type="password" id="outgoing_server_password" name="outgoing_server_password" size="32" maxlength="64" value="<?php echo $this->settings->outgoing_server_password; ?>" />
	</td>
</tr>
</table>
</fieldset>
</div><!-- #email close -->

<?php endif; ?>

<?php if ($this->config->get('enable_contacts')) : ?>

<div id="contacts" class="mootabs_panel">
<fieldset class="josform">
<legend><?php echo text::_( _LANG_CONTACTS_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="ioffice_edit">
<tr>
	<td width="30%">
		<label id="enable_contacts" for="enable_contacts">
			<?php echo _LANG_SETTINGS_ENABLE_CONTACTS; ?>:
		</label>
	</td>
	<td>
		<select id="enable_contacts" name="enable_contacts">
			<option value="0" <?php if ($this->settings->enable_contacts == 0) echo 'selected'; ?>>No</option>
			<option value="1" <?php if ($this->settings->enable_contacts == 1) echo 'selected'; ?>>Yes</option>
		</select>
	</td>
</tr>
</table>
</fieldset>
</div><!-- #contacts close -->

<?php endif; ?>

</div><!-- #myTabs close -->

<div style="clear:both; margin-top:30px;"></div>

<button class="button" type="button" onclick="submitbutton();return false;"><?php echo text::_('Save'); ?></button>

<input type="hidden" name="id" value="<?php echo $this->settings->id; ?>" />
<input type="hidden" name="userid" value="<?php echo $this->user->get('id'); ?>" />
<input type="hidden" name="username" value="<?php echo $this->user->get('username');?>" />
<input type="hidden" name="gid" value="<?php echo $this->user->get('gid');?>" />
<input type="hidden" name="option" value="com_intranetoffice" />
<input type="hidden" name="task" value="save_settings" />
<input type="hidden" name="type" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>
