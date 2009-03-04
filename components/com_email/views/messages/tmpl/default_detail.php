<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<script type="text/javascript">
$(document).ready(function() {
	$("#dialog").css("visibility", "hidden" );
	$("#dialog").css("position", "absolute" );

	$("a#move_email").click(function() {
		$("#dialog").css("visibility", "visible" );
		
		$("#dialog").dialog({
			bgiframe: true,
			height: 300,
			modal: true,
			buttons: {
				'Create user account': function() {
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
		
	});

});
</script>

<div id="dialog" title="Move message to folder...">
	
	<form action="index.php" method="post" name="iofficemailform">

	<fieldset>
	
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td>Current folder:</td>
	<td>
	<?php echo $this->folder; ?>
	</td>
	</tr>
	<tr>
	<td>New folder:</td>
	<td>
	<select class="inputbox" name="mailbox">
		<option value="">/</option>
		<?php foreach ($this->boxes as $box) : ?>
		<option value="<?php echo $box['nameX'] ?>"><?php echo $box['nameX'] ?></option>
		<?php endforeach; ?>
	</select>
	</td>
	</tr>
	</table>
	
	</fieldset>
	
	<input type="hidden" name="option" value="com_intranetoffice" />
	<input type="hidden" name="task" value="move_email" />
	<input type="hidden" name="view" value="email" />
	<input type="hidden" name="type" value="move_email" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
	<input type="hidden" name="uid" value="" />
	<?php echo html::_( 'form.token' ); ?>
	
	</form>
	
</div>

<script language="javascript" type="text/javascript">
var selected_rows = new Array();
selected_rows[<?php echo $this->message['uid']; ?>] = <?php echo $this->message['uid']; ?>;

function confirm_delete(trash, uid, label) {
	var remove_link = "index.php?option=com_email&task=remove_email&folder=<?php echo text::_($this->folder, true); ?>";
	
	if (trash) {
		remove_link += "&trash=1";
	}
	
	// if no uid is passed we use the selected rows array
	if (typeof uid == 'undefined' ) {
		if (trash) var msg = "Are you sure you want to move the selected messages to the trash folder?";
		else var msg = "Are you sure you want to flag the selected messages as deleted?";
		var answer = confirm(msg);
		
		if (answer){
			window.location = remove_link+"&uid="+uid.toString();
		}
	}
	else {
		if (trash) var msg = "Are you sure you want to move email '"+label+"' to trash folder?";
		else var msg = "Are you sure you want to flag email '"+label+"' as deleted?";
		var answer = confirm(msg);
	
		if (answer){
			window.location = remove_link+"&uid="+uid;
		}
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<?php //echo '<pre>'; var_dump($this->message); echo '<pre>'; exit; ?>

<div id="email_detail_actions">
	<div>
		<a href="Javascript:window.history.back();" title="<?php echo text::_( _LANG_BACK ); ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/generic/32x32/back.png" alt="<?php echo text::_( _LANG_BACK ); ?>" />
		</a>
	</div>
	<div>
		<a href="<?php echo route::_("index.php?option=com_email&view=email&type=reply&folder=".$this->folder."&uid=".$this->message['uid']); ?>" title="<?php echo _LANG_REPLY; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/mail_reply-32x32.png" alt="<?php echo _LANG_REPLY; ?>" title="<?php echo _LANG_REPLY; ?>" />
		</a>
	</div>
	<div>
		<a href="<?php echo route::_("index.php?option=com_email&view=email&type=reply_all&folder=".$this->folder."&uid=".$this->message['uid']); ?>" title="<?php echo _LANG_REPLY_TO_ALL; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/mail_replyall-32x32.png" alt="<?php echo _LANG_REPLY_TO_ALL; ?>" title="<?php echo _LANG_REPLY_TO_ALL; ?>" />
		</a>
	</div>
	<div>
		<a href="<?php echo route::_("index.php?option=com_email&view=email&type=forward&folder=".$this->folder."&uid=".$this->message['uid']); ?>" title="<?php echo _LANG_FORWARD; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/mail_forward-32x32.png" alt="<?php echo _LANG_FORWARD; ?>" title="<?php echo _LANG_FORWARD; ?>" />
		</a>
	</div>
	<div>
		<a id="move_email" title="<?php echo _LANG_EMAIL_MOVE_TO_FOLDER; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/move_email.png" alt="<?php echo _LANG_EMAIL_MOVE_TO_FOLDER; ?>" />
		</a>
	</div>
	<div>
		<a href="Javascript:confirm_delete(1, <?php echo $this->message['uid']; ?>, '<?php echo $this->message['subject']; ?>');" title="<?php echo _LANG_EMAIL_MOVE_TO_TRASH; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/move_to_trash.png" alt="<?php echo _LANG_EMAIL_MOVE_TO_TRASH; ?>" />
		</a>
	</div>
	<!-- 
	<div>
		<a href="Javascript:confirm_delete(<?php echo $this->message['uid']; ?>, '<?php echo substr(imap_utf8($this->message['subject']), 0, 32); if (strlen($this->message['subject']) > 33) { echo '...'; }; ?>');" title="<?php echo _LANG_DELETE; ?>">
		<img border="0" src="templates/<?php echo $this->config->get('template'); ?>/images/icons/email/32x32/mail_delete-32x32.png" alt="<?php echo _LANG_DELETE; ?>" title="<?php echo _LANG_DELETE; ?>" />
		</a>
	</div>
	 -->
</div>
<div style="clear:both;"></div>

<br />

<table class="ioffice_email_detail" width="100%" cellpadding="0" cellspacing="0">
<tr>
<th><?php echo _LANG_EMAIL_FROM; ?>:</th>
<td><?php echo $this->message['from_name']." &lt;".$this->message['from_address']."&gt;"; ?></td>
</tr>
<tr>
<th><?php echo _LANG_EMAIL_SUBJECT; ?>:</th>
<td><?php echo $this->message['subject']; ?></td>
</tr>
<tr>
<th><?php echo _LANG_EMAIL_DATE; ?>:</th>
<td><?php echo $this->message['date']; ?></td>
</tr>
<tr>
<th><?php echo _LANG_EMAIL_TO; ?>:</th>
<td><?php echo $this->message['to_name']." &lt;".$this->message['to_address']."&gt;"; ?></td>
</tr>
<tr>
<th><?php echo _LANG_REPLY_TO; ?>:</th>
<td><?php echo $this->message['reply_toaddress']; ?></td>
</tr>
<?php if (is_array($this->message['attachments']) && count($this->message['attachments']) > 0) : ?>
<tr>
<th valign="top"><?php echo _LANG_ATTACHMENTS; ?></th>
<td>
<?php for ($x=0; $x<count($this->message['attachments']); $x++) : ?>
	<?php $attachment = $this->message['attachments'][$x]; ?>
	<?php if (!empty($attachment['file_name'])) : ?>
	<a href="index.php?option=com_email&amp;task=download_attachment&amp;view=email&folder=<?php echo $this->folder; ?>&amp;file_name=<?php echo $attachment['file_name']; ?>&amp;msgno=<?php echo $this->message['msgno'] ?>&amp;file=<?php echo $x; ?>">
	<?php echo $attachment['file_name']; ?>
	</a> - <?php echo text::bytes($attachment['file_size']); ?> <br />
	<?php endif; ?>
<?php endfor; ?>
</td>
</tr>
<?php endif; ?>
</table>

<br />

<div class="email_detail_body">
<?php echo $this->message['body']; ?>
</div>