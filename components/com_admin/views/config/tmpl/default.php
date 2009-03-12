<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 * 
 * @todo	The form in this file needs validation before submit.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<!-- add jQuery accordion behaviour -->
<script type="text/javascript">
	$(function() {
		$("#accordion").accordion({
			autoHeight: false
		});
	});
</script>


<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" name="configform">
	
<div id="accordion">
	
	<h3><a href="#"><?php echo text::_( _LANG_GENERAL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SITENAME ); ?></td>
			<td><input type="text" size="40" name="sitename" value="<?php echo $this->config->sitename; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_TEMPLATE ); ?></td>
			<td>
				<select name="template">
					<option value="default" <?php if ($this->config->template == 'default') { echo 'selected'; } ?>>Default</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DEFAULT_LANG ); ?></td>
			<td>
				<select name="default_lang">
					<option value="en-GB" <?php if ($this->config->default_lang == 'en-GB') { echo 'selected'; } ?>>en-GB</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DEBUG ); ?></td>
			<td>
				<select name="debug">
					<option value="0" <?php if ($this->config->debug == "0") { echo 'selected'; } ?>>No</option>
					<option value="1" <?php if ($this->config->debug == "1") { echo 'selected'; } ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SECRET ); ?></td>
			<td><input type="text" size="40" name="secret" value="<?php echo $this->config->secret; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo text::_( _LANG_DATABASE_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DB_HOST ); ?></td>
			<td><input type="text" size="40" name="db_host" value="<?php echo $this->config->db_host; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DB_USER ); ?></td>
			<td><input type="text" size="40" name="db_user" value="<?php echo $this->config->db_user; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DB_PASS ); ?></td>
			<td><input type="password" size="40" name="db_pass" value="<?php echo $this->config->db_pass; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DB_NAME ); ?></td>
			<td><input type="text" size="40" name="db_name" value="<?php echo $this->config->db_name; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_DB_PREFIX ); ?></td>
			<td><input type="text" size="40" name="db_prefix" value="<?php echo $this->config->db_prefix; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo text::_( _LANG_FILESYSTEM_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_UPLOAD_DIR ); ?></td>
			<td><input type="text" size="40" name="upload_dir" value="<?php echo $this->config->upload_dir; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_FILESYSTEM ); ?></td>
			<td><input type="text" size="40" name="filesystem" value="<?php echo $this->config->filesystem; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_UPLOAD_ACCEPT ); ?></td>
			<td><input type="text" size="40" name="upload_accept" value="<?php echo $this->config->upload_accept; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo text::_( _LANG_INCOMING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_IMAP_HOST ); ?></td>
			<td><input type="text" size="40" name="imap_host" value="<?php echo $this->config->imap_host; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_IMAP_PORT ); ?></td>
			<td><input type="text" size="5" name="imap_port" value="<?php echo $this->config->imap_port; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_IMAP_USER ); ?></td>
			<td><input type="text" size="40" name="imap_user" value="<?php echo $this->config->imap_user; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_IMAP_PASS ); ?></td>
			<td><input type="password" size="40" name="imap_password" value="<?php echo $this->config->imap_password; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo text::_( _LANG_OUGOING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_MAILER ); ?></td>
			<td>
				<select name="mailer">
					<option value="mail" <?php if ($this->config->mailer == 'mail') { echo 'selected'; } ?>>mail</option>
					<option value="sendmail" <?php if ($this->config->mailer == 'sendmail') { echo 'selected'; } ?>>sendmail</option>
					<option value="smtp" <?php if ($this->config->mailer == 'smtp') { echo 'selected'; } ?>>smtp</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_HOST ); ?></td>
			<td><input type="text" size="40" name="smtp_host" value="<?php echo $this->config->smtp_host; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_PORT ); ?></td>
			<td><input type="text" size="5" name="smtp_port" value="<?php echo $this->config->smtp_port; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_AUTH ); ?></td>
			<td>
				<select name="smtp_auth">
					<option value="0" <?php if ($this->config->smtp_auth == "0") { echo 'selected'; } ?>>No</option>
					<option value="1" <?php if ($this->config->smtp_auth == "1") { echo 'selected'; } ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_USER ); ?></td>
			<td><input type="text" size="40" name="smtp_user" value="<?php echo $this->config->smtp_user; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_PASS ); ?></td>
			<td><input type="password" size="40" name="smtp_password" value="<?php echo $this->config->smtp_password; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_FROMADDRESS ); ?></td>
			<td><input type="text" size="40" name="fromaddress" value="<?php echo $this->config->fromaddress; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo text::_( _LANG_CONFIG_SMTP_FROMNAME ); ?></td>
			<td><input type="text" size="40" name="fromname" value="<?php echo $this->config->fromname; ?>" /></td>
		</tr>
		</table>
	</div>
	
</div><!-- close #accordion -->
	
<br style="clear: left;" />
<br />

<?php 
if (request::getVar('tmpl') != 'component') {
	html::button('button', _LANG_BACK, "window.location = 'index.php?option=com_admin';");
	html::button('submit', _LANG_SAVE);
}
?>
	
<input type="hidden" name="option" value="com_admin" />
<input type="hidden" name="task" value="save_config" />
	
</form>