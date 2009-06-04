<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<!-- initialize squeezebox (lightbox) -->
<script type="text/javascript">
window.addEvent('domready', function() {
	
	/* Lighbox iframe for new phpFrame_Mail_IMAP folders form */
	SqueezeBox.initialize({});

	$$('a.modal').each(function(el) {
		el.addEvent('click', function(e) {
			new Event(e).stop();
			SqueezeBox.fromElement(el);
		});
	});
	
});
</script>

<script language="javascript" type="text/javascript">
var attachments = new Array();

function submitbutton() {
	var form = document.iofficemailform;

	// do field validation
	if (form.recipients.value == "") {
		alert('<?php echo phpFrame_HTML_Text::_( _LANG_EMAIL_TO_REQUIRED , true); ?>');
		return;
	}
	
	storeAttachmentsInForm();
	
	form.submit();
}

function storeAttachmentsInForm() {
	var form = document.iofficemailform;
	var paths_array = new Array();
	
	for (var i=0; i<attachments.length; i++) {
		paths_array[i] = attachments[i][0];
	}
	
	form.attachments.value = paths_array;
}

function add_attachment(path, size, type) {
	// Build array with new attachment data
	var attachment = new Array();
	attachment[0] = path;
	attachment[1] = size;
	attachment[2] = type;
	
	// Add attachment to global attachments array
	var i = attachments.length;
	attachments[i] = attachment;
	//alert(attachments.length);
	
	// Update td showing attachments
	var attachments_td = document.getElementById('attachments_td');
	var attachments_html = '';
	for (var k=0; k<attachments.length; k++) {
		if (k>0) {
			attachments_html += "<br />";
		}
		attachments_html += attachments[k][0]+" ("+attachments[k][1]+" Bytes)";
	}
	attachments_td.innerHTML = attachments_html;
}
</script>

<div class="componentheading"><?php echo $data['page_title']; ?></div>

<div id="email_detail_actions">
	<div>
		<a href="Javascript:window.history.back();" title="<?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?>">
		<img border="0" src="templates/<?php echo config::TEMPLATE; ?>/images/icons/generic/32x32/back.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?>" />
		</a>
	</div>
	<div>
		<a href="Javascript:submitbutton();" title="<?php echo phpFrame_HTML_Text::_( _LANG_SEND ); ?>">
		<img border="0" src="templates/<?php echo config::TEMPLATE; ?>/images/icons/email/32x32/mail_send-32x32.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_SEND ); ?>" />
		</a>
	</div>
	<div>
		<a class="modal" href="index.php?component=com_intranetoffice&amp;view=email&amp;folder=<?php echo $this->folder; ?>&amp;type=new_attachment&tmpl=component" rel="{handler: 'iframe', size: {x: 340, y: 160}}">
		<img border="0" src="templates/<?php echo config::TEMPLATE; ?>/images/icons/email/32x32/attach.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_ATTACH ); ?>" />
		</a>
	</div>
	<div>
		<a href="#" title="<?php echo phpFrame_HTML_Text::_( _LANG_SAVE_DRAFT ); ?>">
		<img border="0" src="templates/<?php echo config::TEMPLATE; ?>/images/icons/generic/32x32/save.png" alt="<?php echo phpFrame_HTML_Text::_( _LANG_SAVE_DRAFT ); ?>" />
		</a>
	</div>
</div>
<div style="clear:both;"></div>

<br />

<form action="index.php" method="post" name="iofficemailform">

<table class="ioffice_email_detail" width="100%" cellpadding="0" cellspacing="0">
<tr>
<th><?php echo _LANG_EMAIL_FROM; ?>: </th>
<td><?php echo $this->account->fromname.' &lt;'.$this->account->email_address.'&gt;'; ?></td>
</tr>
<tr>
<th><?php echo _LANG_EMAIL_TO; ?>: </th>
<td><input type="text" name="recipients" value="<?php echo $this->to; ?>" size="50" /></td>
</tr>
<tr>
<th>CC: </th>
<td><input type="text" name="cc" value="" size="50" /></td>
</tr>
<tr>
<th>BCC: </th>
<td><input type="text" name="bcc" value="" size="50" /></td>
</tr>
<tr>
<th><?php echo _LANG_EMAIL_SUBJECT; ?>: </th>
<td><input type="text" name="subject" value="<?php echo $this->subject; ?>" size="50" /></td>
</tr>
<tr>
<th><?php echo _LANG_ATTACHMENTS; ?>: </th>
<td id="attachments_td"></td>
</tr>
<tr>
<td colspan="2">
	<?php echo _LANG_EMAIL_SAVE_COPY_IN_SENT_FOLDER; ?>: 
	<input type="checkbox" name="save_in_sent" value="1" />
</td>
</tr>
</table>

<br />

<div class="email_detail_body">
<textarea name="body" rows="18" cols="100">

<?php echo $this->account->email_signature; ?>

<?php if (!empty($this->body)) : ?>
<?php echo $this->body; ?>
<?php endif; ?>
</textarea>
</div>

<input type="hidden" name="from_address" value="<?php echo $this->account->email_address; ?>" />
<input type="hidden" name="fromname" value="<?php echo $this->account->fromname; ?>" />
<input type="hidden" name="attachments" value="" />
<input type="hidden" name="flag" value="<?php echo $this->message['uid']."|".phpFrame::getRequest()->get('type', 'list'); ?>" />
<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
<input type="hidden" name="component" value="com_intranetoffice" />
<input type="hidden" name="action" value="send_email" />
</form>