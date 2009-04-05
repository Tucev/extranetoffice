<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Add confirm behaviour to delete links
phpFrame_HTML::confirm('delete_issue', _LANG_PROJECTS_ISSUES_DELETE, _LANG_PROJECTS_ISSUES_DELETE_CONFIRM);
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
	
	<?php if ($this->row->created_by == $this->user->id) : ?>
	<div class="thread_delete">
		<a class="delete_issue" title="<?php echo phpFrame_HTML_Text::_($this->row->title, true); ?>" href="index.php?option=com_projects&task=remove_issue&projectid=<?php echo $this->row->projectid; ?>&issueid=<?php echo $this->row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_edit">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=issues&layout=form&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<?php if ($this->row->closed == "0000-00-00 00:00:00") : ?>
	<div class="thread_close">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=close_issue&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_ISSUES_CLOSE ); ?>
		</a>
	</div>
	<?php else : ?>
	<div class="thread_reopen">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=reopen_issue&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_ISSUES_REOPEN ); ?>
		</a>
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
		<?php echo $this->row->title; ?>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($this->row->created)); ?>
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
    	<br />
    	<?php echo _LANG_DTSTART; ?>: <?php if ($this->row->dtstart != '0000-00-00') { echo date("d M Y", strtotime($this->row->dtstart)); } else { echo _LANG_NOT_SET; } ?> 
    	- 
    	<?php echo _LANG_DTEND; ?>: <?php if ($this->row->dtend != '0000-00-00') { echo date("d M Y", strtotime($this->row->dtend)); } else { echo _LANG_NOT_SET; } ?>
	</div>
	
	<?php if (!empty($this->row->description)) : ?>
	<div class="thread_body">
		<?php echo nl2br($this->row->description); ?>
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
		<?php echo _LANG_ISSUES_CLOSE; ?>: <input type="checkbox" name="close_issue" />
		</p>
		<p>
		<button class="button"><?php echo _LANG_COMMENTS_SEND; ?></button>
		</p>
		<input type="hidden" name="option" value="com_projects" />
		<input type="hidden" name="task" value="save_comment" />
		<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
		<input type="hidden" name="type" value="issues" />
		<input type="hidden" name="itemid" value="<?php echo  $this->row->id; ?>" />
		<input type="hidden" name="issueid" value="<?php echo  $this->row->id; ?>" />
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