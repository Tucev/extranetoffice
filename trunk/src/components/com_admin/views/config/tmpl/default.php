<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * 
 * @todo	The form in this file needs validation before submit.
 */
?>

<!-- add jQuery accordion behaviour -->
<script type="text/javascript">
	$(function() {
		$("#accordion").accordion({
			autoHeight: false
		});

		<?php if (PHPFrame::Request()->get('tmpl') == 'component') : ?>
		$("#configform").submit(function() {
			// bind form using 'ajaxForm'
			var ajax_container = $("div#"+$sysadmin_selected_tab);
			
			// submit the form 
			$(this).ajaxSubmit({ target: ajax_container });

			// Add the loading div inside the ajax container
			$("div#"+$sysadmin_selected_tab).html('<div class="loading"></div>');
			
			// return false to prevent normal browser submit and page navigation 
			return false;
		});
		
	 	// Bind AJAX events to loading div to show/hide animation
		$(".loading").bind("ajaxSend", function() {
			$(this).show();
		})
		.bind("ajaxComplete", function() {
			   $(this).hide();
		});
	    <?php endif; ?>
	});
</script>


<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<form action="index.php" method="post" id="configform" name="configform">
	
<div id="accordion">
	
	<h3><a href="#"><?php echo PHPFrame_Base_String::html( _LANG_GENERAL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SITENAME ); ?></td>
			<td><input type="text" size="40" name="sitename" value="<?php echo config::SITENAME; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_TEMPLATE ); ?></td>
			<td>
				<select name="template">
					<option value="default" <?php if (config::THEME == 'default') { echo 'selected'; } ?>>Default</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DEFAULT_LANG ); ?></td>
			<td>
				<select name="default_lang">
					<option value="en-GB" <?php if (config::DEFAULT_LANG == 'en-GB') { echo 'selected'; } ?>>en-GB</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_TIMEZONE ); ?></td>
			<td>
				<select name="timezone">
					<option value="Europe/London" <?php if (config::TIMEZONE == 'Europe/London') { echo 'selected'; } ?>>Europe/London</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DEBUG ); ?></td>
			<td>
				<select name="debug">
					<option value="0" <?php if (config::DEBUG == "0") { echo 'selected'; } ?>>No</option>
					<option value="1" <?php if (config::DEBUG == "1") { echo 'selected'; } ?>>Yes</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_LOG_LEVEL ); ?></td>
			<td>
				<select name="log_level">
					<option value="1" <?php if (config::LOG_LEVEL == "1") { echo 'selected'; } ?>>Errors only</option>
					<option value="2" <?php if (config::LOG_LEVEL == "2") { echo 'selected'; } ?>>Warnings and errors</option>
					<option value="3" <?php if (config::LOG_LEVEL == "3") { echo 'selected'; } ?>>Notices, warnings and errors</option>
				</select>
			</td>
		</tr>
		
		
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_LOG_FILE ); ?></td>
			<td><input type="text" size="40" name="log_file" value="<?php echo config::LOG_FILE; ?>" /></td>
		</tr>
		
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SECRET ); ?></td>
			<td><input type="text" size="40" name="secret" value="<?php echo config::SECRET; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo PHPFrame_Base_String::html( _LANG_DATABASE_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DB_HOST ); ?></td>
			<td><input type="text" size="40" name="db_host" value="<?php echo config::DB_HOST; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DB_USER ); ?></td>
			<td><input type="text" size="40" name="db_user" value="<?php echo config::DB_USER; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DB_PASS ); ?></td>
			<td><input type="password" size="40" name="db_pass" value="<?php echo config::DB_PASS; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DB_NAME ); ?></td>
			<td><input type="text" size="40" name="db_name" value="<?php echo config::DB_NAME; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_DB_PREFIX ); ?></td>
			<td><input type="text" size="40" name="db_prefix" value="<?php echo config::DB_PREFIX; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo PHPFrame_Base_String::html( _LANG_FILESYSTEM_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_UPLOAD_DIR ); ?></td>
			<td><input type="text" size="40" name="upload_dir" value="<?php echo config::UPLOAD_DIR; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_FILESYSTEM ); ?></td>
			<td><input type="text" size="40" name="filesystem" value="<?php echo config::FILESYSTEM; ?>" /></td>
		</tr>
		
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_MAX_UPLOAD_SIZE ); ?></td>
			<td><input type="text" size="6" name="max_upload_size" value="<?php echo config::MAX_UPLOAD_SIZE; ?>" /> Mb</td>
		</tr>
		
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_UPLOAD_ACCEPT ); ?></td>
			<td><input type="text" size="40" name="upload_accept" value="<?php echo config::UPLOAD_ACCEPT; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo PHPFrame_Base_String::html( _LANG_INCOMING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_IMAP_HOST ); ?></td>
			<td><input type="text" size="40" name="imap_host" value="<?php echo config::IMAP_HOST; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_IMAP_PORT ); ?></td>
			<td><input type="text" size="5" name="imap_port" value="<?php echo config::IMAP_PORT; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_IMAP_USER ); ?></td>
			<td><input type="text" size="40" name="imap_user" value="<?php echo config::IMAP_USER; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_IMAP_PASS ); ?></td>
			<td><input type="password" size="40" name="imap_password" value="<?php echo config::IMAP_PASSWORD; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo PHPFrame_Base_String::html( _LANG_OUGOING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_MAILER ); ?></td>
			<td>
				<select name="mailer">
					<option value="mail" <?php if (config::MAILER == 'mail') { echo 'selected'; } ?>>mail</option>
					<option value="sendmail" <?php if (config::MAILER == 'sendmail') { echo 'selected'; } ?>>sendmail</option>
					<option value="smtp" <?php if (config::MAILER == 'smtp') { echo 'selected'; } ?>>smtp</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_HOST ); ?></td>
			<td><input type="text" size="40" name="smtp_host" value="<?php echo config::SMTP_HOST; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_PORT ); ?></td>
			<td><input type="text" size="5" name="smtp_port" value="<?php echo config::SMTP_PORT; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_AUTH ); ?></td>
			<td>
				<select name="smtp_auth">
					<option value="0" <?php if (config::SMTP_AUTH == "0") { echo 'selected'; } ?>>No</option>
					<option value="1" <?php if (config::SMTP_AUTH == "1") { echo 'selected'; } ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_USER ); ?></td>
			<td><input type="text" size="40" name="smtp_user" value="<?php echo config::SMTP_USER; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_PASS ); ?></td>
			<td><input type="password" size="40" name="smtp_password" value="<?php echo config::SMTP_PASSWORD; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_FROMADDRESS ); ?></td>
			<td><input type="text" size="40" name="fromaddress" value="<?php echo config::FROMADDRESS; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo PHPFrame_Base_String::html( _LANG_CONFIG_SMTP_FROMNAME ); ?></td>
			<td><input type="text" size="40" name="fromname" value="<?php echo config::FROMNAME; ?>" /></td>
		</tr>
		</table>
	</div>
	
</div><!-- close #accordion -->
	
<br style="clear: left;" />
<br />

<?php 
if (PHPFrame::Request()->get('tmpl') != 'component') {
	PHPFrame_HTML::button('button', _LANG_BACK, "window.location = 'index.php?component=com_admin';");
}
else {
	?><input type="hidden" name="tmpl" value="component" /><?php
}

PHPFrame_HTML::button('submit', _LANG_SAVE);
?>
	
<input type="hidden" name="component" value="com_admin" />
<input type="hidden" name="action" value="save_config" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>
</form>