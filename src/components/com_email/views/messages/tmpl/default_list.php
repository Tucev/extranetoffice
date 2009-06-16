<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<!-- initialize context menu -->
<script type="text/javascript">
	SimpleContextMenu.setup({'preventDefault':false, 'preventForms':false});
</script>

<script language="javascript" type="text/javascript">
var selected_rows = new Array();

function select_row(el, uid) {
	if (uid in selected_rows) {
		el.style.backgroundColor = '';
		selected_rows.splice(uid, 1);
	}
	else {
		// Save original row colour to be able to restore it when unselected
		var original_row_colour = el.style.backgroundColor;
		// Change background color to indicate selection
		el.style.backgroundColor = '#d9e5f9';
		// Store row in array
		selected_rows[uid] = uid;
	}
}

function confirm_delete(trash, uid, label) {
	var remove_link = "index.php?component=com_email&action=remove_email&folder=<?php echo PHPFrame_Base_String::html($this->folder, true); ?>";
	if (trash) {
		remove_link += "&amp;trash=1";
	}
	
	// if no uid is passed we use the selected rows array
	if (typeof uid == 'undefined' ) {
		var uid = new Array();
		for (row in selected_rows) {
			if (!isNaN(row)) uid.push(row);
		}
		
		if (uid.length < 1) {
			alert("No messages selected"); 
		}
		else {
			if (trash) var msg = "Are you sure you want to move the selected messages to the trash folder?";
			else var msg = "Are you sure you want to flag the selected messages as deleted?";
			var answer = confirm(msg);
		
			if (answer){
				window.location = remove_link+"&uid="+uid.toString();
			}
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

function confirm_empty_deleted_items() {
	var answer = confirm("Are you sure you want to empty deleted items in this mail folder?")
	if (answer){
		window.location = "index.php?component=com_email&action=empty_deleted_items&folder=<?php echo PHPFrame_Base_String::html($this->folder, true); ?>";
	}
}

function confirm_empty_trash() {
	var answer = confirm("Are you sure you want to empty the trash folder?")
	if (answer){
		window.location = "index.php?component=com_email&action=empty_email_trash";
	}
}

function confirm_delete_folder(mailbox, label) {
	var remove_link = "index.php?component=com_email&action=delete_mailbox&folder=<?php echo PHPFrame_Base_String::html($this->folder, true); ?>";
	
	var answer = confirm("Are you sure you want to delete email '"+label+"'?")
	
	if (answer){
		window.location = remove_link+"&mailbox="+mailbox;
	}
}
</script>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<div id="right_col">
	
	<div class="module">
	
	<div>
		<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=accounts"); ?>">
		<?php echo PHPFrame_Base_String::html(_LANG_EMAIL_ACCOUNTS); ?>
		</a>
	</div>
	
	<div>
		<select>
			<option value=""></option>
		</select>
	</div>
	
	<hr />
	
	<div style="float:right;" class="new">
		<a class="modal" href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo $this->folder; ?>&amp;layout=new_folder&tmpl=component" rel="{handler: 'iframe', size: {x: 400, y: 160}}">
		<?php echo PHPFrame_Base_String::html(_LANG_EMAIL_NEW_FOLDER); ?>
		</a>
	</div>
	
	<h3>Mail folders</h3>
	
	<?php //echo '<pre>'; var_dump($this->boxes); echo '</pre>'; ?>
	
	<ul>
		<?php if (is_array($this->boxes) && count($this->boxes) > 0) : ?>
			<?php foreach ($this->boxes as $key=>$box) : ?>
			<?php //var_dump($box); exit; ?>
			
			<li class="container_<?php echo $key; ?>">
				<script type="text/javascript">
					SimpleContextMenu.attach('container_<?php echo $key; ?>', 'CM_mailbox_<?php echo $key; ?>');
				</script>
			
				<a href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo $box['nameX'] ?>"><?php echo $box['nameX'] ?></a>
				
				<?php if (strtolower($box['nameX']) != 'inbox') : ?>
				<ul id="CM_mailbox_<?php echo $key; ?>" class="SimpleContextMenu">
				
				<li>
				<a class="modal" href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo $box['nameX']; ?>&amp;layout=rename_folder&tmpl=component" rel="{handler: 'iframe', size: {x: 400, y: 160}}">
				<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/generic/16x16/edit.png" alt="<?php echo _LANG_RENAME; ?>" />
				 <?php echo _LANG_RENAME; ?>
				</a>
				</li>
				
				<li>
				<a href="Javascript:confirm_delete_folder('<?php echo PHPFrame_Base_String::html($box['nameX'], true); ?>', '<?php echo substr($box['nameX'], 0, 32); if (strlen($box['nameX']) > 33) { echo '...'; }; ?>');" title="<?php echo _LANG_DELETE; ?>">
				<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/email/16x16/mail_delete-16x16.png" alt="<?php echo _LANG_DELETE; ?>" />
				 <?php echo _LANG_DELETE; ?>
				</a>
				</li>
				
				</ul>
				<?php endif; ?>
				
			</li>
			
			<?php endforeach;?>
		<?php else : ?>
		<?php echo PHPFrame_Base_String::html( _LANG_NO_MAILBOXES ); ?>
		<?php endif; ?>
				
	</ul>
	
	</div><!-- close .module -->
	
</div><!-- close .ioffice_right_col -->

<div id="main_col_2">

<div id="email_detail_actions">
	<div>
		<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&amp;view=messages&amp;layout=form"); ?>" title="<?php echo PHPFrame_Base_String::html( _LANG_NEW ); ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/generic/32x32/new.png" alt="<?php echo PHPFrame_Base_String::html( _LANG_NEW ); ?>" />
		</a>
	</div>
	<div>
		<a class="modal" href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo PHPFrame_Base_String::html($this->folder, true); ?>&amp;layout=move_email&tmpl=component" rel="{handler: 'iframe', size: {x: 400, y: 160}}" title="<?php echo _LANG_EMAIL_MOVE_TO_FOLDER; ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/email/32x32/move_email.png" alt="<?php echo _LANG_EMAIL_MOVE_TO_FOLDER; ?>" />
		</a>
	</div>
	<div>
		<a href="Javascript:confirm_delete(1);" title="<?php echo _LANG_EMAIL_MOVE_TO_TRASH; ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/email/32x32/move_to_trash.png" alt="<?php echo _LANG_EMAIL_MOVE_TO_TRASH; ?>" />
		</a>
	</div>
	<div>
		<a href="Javascript:confirm_empty_trash();" title="<?php echo _LANG_EMAIL_EMPTY_TRASH; ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/generic/32x32/empty_trash.png" alt="<?php echo _LANG_EMAIL_EMPTY_TRASH; ?>" />
		</a>
	</div>
	<!-- 
	<div>
		<a href="Javascript:confirm_delete();" title="<?php echo _LANG_EMAIL_FLAG_DELETED; ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/generic/32x32/remove.png" alt="<?php echo _LANG_EMAIL_FLAG_DELETED; ?>" />
		</a>
	</div>
	<div>
		<a href="Javascript:confirm_empty_deleted_items();" title="<?php echo _LANG_EMAIL_EMPTY_TRASH; ?>">
		<img border="0" src="templates/<?php echo config::THEME; ?>/images/icons/generic/32x32/empty_trash.png" alt="<?php echo _LANG_EMAIL_EMPTY_TRASH; ?>" />
		</a>
	</div>
	 -->
</div>
<div style="clear:left;"></div>

<br />

<div id="mailbox-info-left">
	<div>
	Current system time: <?php echo $this->messages['check']->Date; ?><br />
  	Number of messages: <?php echo $this->messages['check']->Nmsgs; ?><br />
	Mailbox Size: <?php echo PHPFrame_Base_Number::bytes($this->messages['check']->Size); ?>
	</div>
</div>
<div id="mailbox-info-right">
	<div>
	Recent messages: <?php echo $this->messages['check']->Recent; ?><br />
	Unread messages: <?php echo $this->messages['check']->Unread; ?><br />
	Deleted messages: <?php echo $this->messages['check']->Deleted; ?>
	</div>
</div>
<div style="clear:left; margin-bottom:10px;"></div>

<form action="index.php" method="post" name="iofficeform">

<?php if (is_array($this->messages['res']) && count($this->messages['res']) > 0) : ?>
<div class="ioffice_table">

	<div class="ioffice_row_heading">
		<div class="ioffice_cell icon_16"></div>
		<div class="ioffice_cell from"><?php echo _LANG_EMAIL_FROM; ?></div>
		<div class="ioffice_cell subject"><?php echo _LANG_EMAIL_SUBJECT; ?></div>
		<div class="ioffice_cell size"><?php echo _LANG_EMAIL_SIZE; ?></div>
		<div class="ioffice_cell date"><?php echo _LANG_EMAIL_DATE; ?></div>
		<div class="ioffice_cell"></div>
		<div style="clear: left"></div>
	</div><!--  close row -->


	<?php $k = 0; ?>
	<?php foreach($this->messages['res'] as $email) : ?>
  	<script type="text/javascript">
		SimpleContextMenu.attach('container_<?php echo $email->uid; ?>', 'CM_<?php echo $email->uid; ?>');
	</script>
	<?php //var_dump($email); //status = ["answered"] ["deleted"] ["seen"] ?>
	<?php 
  	if ($email->answered == 1) { 
  		$status_icon = '<img src="templates/'.config::THEME.'/images/icons/email/replied.png" alt="Unread" />';
  	}
  	elseif ($email->seen == 0) {
  		$status_icon = '<img src="templates/'.config::THEME.'/images/icons/email/new.png" alt="Unread" />';
  	}
  	else { 
  		$status_icon = '<img src="templates/'.config::THEME.'/images/icons/email/read.png" alt="Read" />'; 
  	}
  	?>
  	<div onclick="select_row(this, <?php echo $email->uid; ?>);" class="ioffice_row<?php echo $k; ?> seen<?php echo $email->seen; ?> <?php if ($email->deleted == 1) echo 'deleted'; ?> container_<?php echo $email->uid; ?>">
		<div class="ioffice_cell icon_16">
			<?php echo $status_icon; ?> 
			<?php echo $attachment_icon; ?>
		</div>
		<div class="ioffice_cell from">
			<a class="bold" href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo $this->folder; ?>&amp;layout=detail&amp;uid=<?php echo $email->uid; ?>">
			<?php echo substr($email->from, 0, 40); if (strlen($email->from) > 41) { echo '...'; } ?>
			</a>
		</div>
		<div class="ioffice_cell subject">
    		<a class="bold" href="index.php?component=com_email&amp;view=messages&amp;folder=<?php echo $this->folder; ?>&amp;layout=detail&amp;uid=<?php echo $email->uid; ?>">
    		<?php echo substr($email->subject, 0, 64); if (strlen($email->subject) > 65) { echo '...'; } ?>
    		</a>
    	</div>
    	<div class="ioffice_cell size">
    		<?php echo PHPFrame_Base_Number::bytes($email->size); ?>
    	</div>
    	<div class="ioffice_cell date">
    		<?php echo date("d M Y H:i:s", strtotime($email->date)); ?>
    	</div>
    	<div class="ioffice_cell <?php if ($email->deleted == 1) echo 'undelete'; ?>" style="text-decoration: none;">
	    
	    	<?php if ($email->deleted == 1) : ?>
	    	
	    	<a href="index.php?component=com_email&amp;action=restore_email&amp;folder=<?php echo $this->folder; ?>&amp;uid=<?php echo $email->uid; ?>">
	    		<?php echo _LANG_EMAIL_UNDELETE; ?>
	    	</a>
		    
	    	<?php else : ?>
		    
		    <ul id="CM_<?php echo $email->uid; ?>" class="SimpleContextMenu">
		    
			<li>
			<a href="index.php?component=com_email&amp;view=messages&amp;layout=reply&amp;folder=<?php echo $this->folder; ?>&amp;uid=<?php echo $email->uid; ?>" title="<?php echo _LANG_EMAIL_REPLY; ?>">
			<img src="templates/<?php echo config::THEME; ?>/images/icons/email/16x16/mail_reply-16x16.png" alt="<?php echo _LANG_EMAIL_REPLY; ?>" />
			 <?php echo _LANG_REPLY; ?>
			</a>
			</li>
			
			<li>
			<a href="index.php?component=com_email&amp;view=messages&amp;layout=forward&amp;folder=<?php echo $this->folder; ?>&amp;uid=<?php echo $email->uid; ?>" title="<?php echo _LANG_FORWARD; ?>">
			<img src="templates/<?php echo config::THEME; ?>/images/icons/email/16x16/mail_forward-16x16.png" alt="<?php echo _LANG_FORWARD; ?>" />
			 <?php echo _LANG_FORWARD; ?>
			</a>
			</li>
			
			<li>
			<a href="Javascript:confirm_delete(<?php echo $email->uid; ?>, '<?php echo substr($email->subject, 0, 32); if (strlen($email->subject) > 33) { echo '...'; }; ?>');" title="<?php echo _LANG_DELETE; ?>">
			<img src="templates/<?php echo config::THEME; ?>/images/icons/email/16x16/mail_delete-16x16.png" alt="<?php echo _LANG_DELETE; ?>" />
			 <?php echo _LANG_DELETE; ?>
			</a>
			</li>
			
			<li>
			<a href="index.php?component=com_email&amp;action=set_flags&amp;view=messages&amp;folder=<?php echo $this->folder; ?>&amp;uid=<?php echo $email->uid; ?>&amp;flag=Seen" title="<?php echo _LANG_EMAIL_MARK_AS_READ; ?>">
			 <?php echo _LANG_EMAIL_MARK_AS_READ; ?>
			</a>
			</li>
			
			<li>
			<a href="index.php?component=com_email&amp;action=clear_flags&amp;view=messages&amp;folder=<?php echo $this->folder; ?>&amp;uid=<?php echo $email->uid; ?>&amp;flag=Seen" title="<?php echo _LANG_EMAIL_MARK_AS_UNREAD; ?>">
			 <?php echo _LANG_EMAIL_MARK_AS_UNREAD; ?>
			</a>
			</li>
			</ul>
			
			<?php endif; ?>
			
		</div>
		<div style="clear: left;"></div>
  	</div><!--  close row -->
  	<?php $k = 1 - $k; ?>
  	<?php endforeach; ?>

</div><!--  close table container -->

<input type="hidden" name="component" value="com_email"  />
<input type="hidden" name="action" value=""  />
<input type="hidden" name="view" value="email"  />
<input type="hidden" name="folder" value="<?php echo $this->folder; ?>"  />
</form>

<br />

<!-- Custom pagination -->
  
<table width="100%">
	<tr>
	  <td align="left">
	    <?php 
		  if ($this->messages['pages'] > 1) {
		    echo 'Pages: ';
			for ($i=0; $i<$this->messages['pages']; $i++) {
		      if ($i > 0) { echo ' - '; }
			  if ($this->messages['current_page'] != ($i+1)) {
			    echo '<a href="'.PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&page='.($i+1)).'&per_page='.$this->messages['per_page'].'">';
			    echo ($i+1);
			    echo '</a>';
			  }
			  else {
			    echo ($i+1);
			  }
		    } 
		  }
		  
		?> 
		
		<form name="form1">
		  Messages per page: <select name="menu1" onChange="window.location = this.options[selectedIndex].value;">
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=5'); ?>" <?php if ($this->messages['per_page'] == '5') { echo 'selected'; } ?>>5</option>
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=10'); ?>" <?php if ($this->messages['per_page'] == '10') { echo 'selected'; } ?>>10</option>
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=20'); ?>" <?php if ($this->messages['per_page'] == '20') { echo 'selected'; } ?>>20</option>
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=25'); ?>" <?php if ($this->messages['per_page'] == '25') { echo 'selected'; } ?>>25</option>
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=50'); ?>" <?php if ($this->messages['per_page'] == '50') { echo 'selected'; } ?>>50</option>
			<option value="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_email&view=messages&folder='.$this->folder.'&order_by='.$this->messages['sorting']['by'].'&order_direction='.$this->messages['sorting']['direction'].'&per_page=100'); ?>" <?php if ($this->messages['per_page'] == '100') { echo 'selected'; } ?>>100</option>
		  </select>
		</form>

	  </td>
	</tr>
</table>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_EMAIL ); ?>
<?php endif; ?>

</div><!-- close #ioffice_main_col -->