<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Add confirm behaviour to delete links
phpFrame_HTML::confirm('delete_message', _LANG_PROJECTS_MESSAGES_DELETE, _LANG_PROJECTS_MESSAGES_DELETE_CONFIRM);
// Load jQuery validation behaviour for forms
phpFrame_HTML::validate('commentsform');
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>

<div class="thread_row0">

	<?php if ($this->row->userid == $this->user->id) : ?>
	<div class="thread_delete">
		<a class="delete_message" title="<?php echo phpFrame_HTML_Text::_($this->row->subject, true); ?>" href="index.php?option=com_projects&task=remove_message&projectid=<?php echo $this->row->projectid; ?>&messageid=<?php echo $this->row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
		<?php echo $this->row->subject; ?>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($this->row->date_sent)); ?>
	</div>
	
	<div class="thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $this->row->created_by_name; ?><br />
		<?php echo _LANG_ASSIGNEES; ?>: 
		<?php if (!empty($this->row->assignees)) : ?>
    	<?php for ($j=0; $j<count($this->row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=users&layout=detail&userid=".$this->row->assignees[$j]['id']); ?>">
    		<?php echo $this->row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
	</div>
	
	<?php if (!empty($this->row->body)) : ?>
	<div class="thread_body">
		<?php echo nl2br($this->row->body); ?>
	</div>
	<?php endif; ?>
	
	<?php if (is_array($this->row->comments) && count($this->row->comments) > 0) : ?>
	<h3><?php echo _LANG_COMMENTS; ?></h3>
	<?php foreach ($this->row->comments as $comment) : ?>
		<div class="comment_row">
			<div style="float:left; margin-right: 10px;">
				<img src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo phpFrame_User_Helper::id2photo($comment->userid); ?>" />
			</div>
			<div style="margin-left: 95px;">
				<div class="comment_details">
					<?php echo $comment->created_by_name; ?> &nbsp;&nbsp;
					<?php echo date("D, d M Y H:ia", strtotime($comment->created)); ?>
				</div>
				<?php echo nl2br($comment->body); ?>
			</div>
		</div>
		<div style="clear: left; margin-bottom: 10px;"></div>
	<?php endforeach; ?>
	<?php endif; ?>
	
</div>

<div>
	<div style="float:left; margin-right: 10px;">
		<img src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($this->settings->photo) ? $this->settings->photo : 'default.png'; ?>" />
	</div>
	<div style="margin-left: 95px;">
		<form method="post" id="commentsform">
		<a id="post-comment"></a>
		<p><?php echo _LANG_COMMENTS_NEW; ?>:</p>
		<textarea class="required" name="body" rows="10" cols="60"></textarea>
		<p>
		<?php echo _LANG_NOTIFY_ASSIGNEES; ?>: <input type="checkbox" name="notify" checked />
		</p>
		<p>
		<button class="button"><?php echo _LANG_COMMENTS_SEND; ?></button>
		</p>
		<input type="hidden" name="option" value="com_projects" />
		<input type="hidden" name="task" value="save_comment" />
		<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
		<input type="hidden" name="type" value="messages" />
		<input type="hidden" name="itemid" value="<?php echo  $this->row->id; ?>" />
		<?php if (is_array($this->row->assignees) && count($this->row->assignees) > 0) : ?>
		<?php foreach ($this->row->assignees as $assignee) : ?>
		<input type="hidden" name="assignees[]" value="<?php echo $assignee['id']; ?>" />
		<?php endforeach; ?>
		<?php endif; ?>
		<?php echo phpFrame_HTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>

<div style="clear: left;"></div>

<?php //echo '<pre>'; var_dump($this->row); echo '</pre>'; ?>