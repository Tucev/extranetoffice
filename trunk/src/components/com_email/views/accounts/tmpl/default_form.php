<?php
/**
 * @version 	$Id: default.php 267 2009-03-18 01:45:29Z luis.montero@e-noise.com $
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 * 
 * @todo	The form in this file needs validation before submit.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Load jQuery validation behaviour for form
phpFrame_HTML::validate('email_accountsform');
?>

<!-- add jQuery accordion behaviour -->
<script type="text/javascript">
	$(function() {
		$("#accordion").accordion({
			autoHeight: false
		});

		<?php if (phpFrame_Environment_Request::getVar('tmpl') == 'component') : ?>
		$("#email_accountsform").submit(function() {
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


<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<form action="index.php" method="post" id="email_accountsform" name="email_accountsform">
	
<div id="accordion">
	
	<h3><a href="#"><?php echo phpFrame_HTML_Text::_( _LANG_INCOMING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_EMAIL_SERVER_TYPE ); ?></td>
			<td>
				<select name="server_type">
					<option value="IMAP">imap</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_IMAP_HOST ); ?></td>
			<td><input class="required" type="text" size="40" name="imap_host" value="<?php echo $this->row->imap_host; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_IMAP_PORT ); ?></td>
			<td><input class="required" type="text" size="5" name="imap_port" value="<?php echo $this->row->imap_port; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_IMAP_USER ); ?></td>
			<td><input class="required" type="text" size="40" name="imap_user" value="<?php echo $this->row->imap_user; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_IMAP_PASS ); ?></td>
			<td><input class="required" type="password" size="40" name="imap_password" value="<?php echo $this->row->imap_password; ?>" /></td>
		</tr>
		</table>
	</div>
	
	<h3><a href="#"><?php echo phpFrame_HTML_Text::_( _LANG_OUGOING_EMAIL_CONFIG ); ?></a></h3>
	<div>
		
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_MAILER ); ?></td>
			<td>
				<select name="mailer">
					<option value="smtp" <?php if ($this->row->mailer == 'smtp') { echo 'selected'; } ?>>smtp</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_HOST ); ?></td>
			<td><input class="required" type="text" size="40" name="smtp_host" value="<?php echo $this->row->smtp_host; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_PORT ); ?></td>
			<td><input class="required" type="text" size="5" name="smtp_port" value="<?php echo $this->row->smtp_port; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_AUTH ); ?></td>
			<td>
				<select name="smtp_auth">
					<option value="0" <?php if ($this->row->smtp_auth == "0") { echo 'selected'; } ?>>No</option>
					<option value="1" <?php if ($this->row->smtp_auth == "1") { echo 'selected'; } ?>>Yes</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_USER ); ?></td>
			<td><input type="text" size="40" name="smtp_user" value="<?php echo $this->row->smtp_user; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_PASS ); ?></td>
			<td><input type="password" size="40" name="smtp_password" value="<?php echo $this->row->smtp_password; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_EMAIL_ADDRESS ); ?></td>
			<td><input class="required" type="text" size="40" name="email_address" value="<?php echo $this->row->email_address; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo phpFrame_HTML_Text::_( _LANG_CONFIG_SMTP_FROMNAME ); ?></td>
			<td><input class="required" type="text" size="40" name="fromname" value="<?php echo $this->row->fromname; ?>" /></td>
		</tr>
		</table>
	</div>
	
</div><!-- close #accordion -->
	
<br style="clear: left;" />
<br />

<button type="button" onclick="window.location = 'index.php?option=com_email&view=accounts';"><?php echo phpFrame_HTML_Text::_(_LANG_BACK); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>
	
<input type="hidden" name="option" value="com_email" />
<input type="hidden" name="task" value="save_account" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>