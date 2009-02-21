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
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<div id="ioffice_right_col">
	<?php require($this->template_path.DS.'menu.html.php'); ?>
</div><!-- close .ioffice_right_col -->

<div id="ioffice_main_col">

<h2 class="subheading <?php echo $this->current_tool; ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'&projectid='.$this->projectid); ?>">
		<?php echo $this->page_subheading; ?>
	</a>
</h2>

<div class="ioffice_thread_row0">
	
	<?php if ($this->row->created_by == $this->user->id) : ?>
	<div class="ioffice_thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $this->row->projectid; ?>, <?php echo $this->row->id; ?>, '<?php echo text::_($this->row->title, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="ioffice_thread_edit">
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=issues_form&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<?php if ($this->row->closed == "0000-00-00 00:00:00") : ?>
	<div class="ioffice_thread_close">
		<a href="<?php echo route::_("index.php?option=com_projects&task=close_issue&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo text::_( _LANG_ISSUES_CLOSE ); ?>
		</a>
	</div>
	<?php else : ?>
	<div class="ioffice_thread_reopen">
		<a href="<?php echo route::_("index.php?option=com_projects&task=reopen_issue&projectid=".$this->project->id."&issueid=".$this->row->id); ?>">
		<?php echo text::_( _LANG_ISSUES_REOPEN ); ?>
		</a>
	</div>
	<?php endif; ?>
	
	<div class="ioffice_thread_heading">
		<?php echo $this->row->title; ?>
	</div>
	
	<div class="ioffice_thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($this->row->created)); ?>
	</div>
	
	<div class="ioffice_thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $this->row->created_by_name; ?><br />
		<?php echo _LANG_ASSIGNEES; ?>: 
		<?php if (!empty($this->row->assignees)) : ?>
    	<?php for ($j=0; $j<count($this->row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo route::_("index.php?option=com_projects&view=users&layout=detail&userid=".$this->row->assignees[$j]['id']); ?>">
    		<?php echo $this->row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
    	<br />
    	<?php echo _LANG_DTSTART; ?>: <?php echo date("d M Y", strtotime($this->row->dtstart)); ?> 
    	<?php echo _LANG_DTEND; ?>: <?php echo date("d M Y", strtotime($this->row->dtend)); ?>
	</div>
	
	<?php if (!empty($this->row->description)) : ?>
	<div class="ioffice_thread_body">
		<?php echo nl2br($this->row->description); ?>
	</div>
	<?php endif; ?>
	
	<?php if (is_array($this->row->comments) && count($this->row->comments) > 0) : ?>
	<h3><?php echo _LANG_COMMENTS; ?></h3>
	<?php foreach ($this->row->comments as $comment) : ?>
		<div class="ioffice_comment_row">
			<div style="float:left; margin-right: 10px;">
				<img src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo usersHelperUsers::id2photo($comment->userid); ?>" />
			</div>
			<div style="margin-left: 95px;">
				<div class="ioffice_comment_details">
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
		<img src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo !empty($this->settings->photo) ? $this->settings->photo : 'default.png'; ?>" />
	</div>
	<div style="margin-left: 95px;">
		<form method="post">
		<a id="post-comment"></a>
		<p><?php echo _LANG_COMMENTS_NEW; ?>:</p>
		<textarea class="inputbox" name="body" rows="10" cols="60"></textarea>
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
		</form>
	</div>
</div>

</div><!-- close #ioffice_main_col -->

<div style="clear: left;"></div>

<?php //echo '<pre>'; var_dump($this->row); echo '</pre>'; ?>