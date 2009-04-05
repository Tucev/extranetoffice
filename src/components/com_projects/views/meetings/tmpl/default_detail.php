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
phpFrame_HTML::confirm('delete_meeting', _LANG_PROJECTS_MEETINGS_DELETE, _LANG_PROJECTS_MEETINGS_DELETE_CONFIRM);
phpFrame_HTML::confirm('delete_slideshow', _LANG_PROJECTS_MEETINGS_SLIDESHOWS_DELETE, _LANG_PROJECTS_MEETINGS_SLIDESHOWS_DELETE_CONFIRM);
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
		<a class="delete_meeting" title="<?php echo phpFrame_HTML_Text::_($this->row->name, true); ?>" href="index.php?option=com_projects&task=remove_meeting&projectid=<?php echo $this->row->projectid; ?>&meetingid=<?php echo $this->row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_edit">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=".phpFrame_Environment_Request::getVar('view')."&layout=form&projectid=".$this->project->id."&meetingid=".$this->row->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<div class="thread_heading">
		<?php echo $this->row->name; ?>
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
    	<?php echo _LANG_DTSTART; ?>: <?php echo date("d M Y", strtotime($this->row->dtstart)); ?><br /> 
    	<?php echo _LANG_DTEND; ?>: <?php echo date("d M Y", strtotime($this->row->dtend)); ?>
	</div>
	
	<?php if (!empty($this->row->description)) : ?>
	<div class="thread_body">
		<?php echo nl2br($this->row->description); ?>
	</div>
	<?php endif; ?>
	

	<div class="thread_new">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=meetings&layout=slideshows_form&projectid=".$this->project->id."&meetingid=".$this->row->id); ?>">
	Add new slideshow
	</a>
	</div>
	
	<h3>Slideshows</h3>
	 
	<?php if (is_array($this->row->slideshows) && count($this->row->slideshows) > 0) : ?>
	
	<?php for ($k=0; $k<count($this->row->slideshows); $k++) : ?>
	<div>
		<div style="margin-bottom: 10px; border-bottom: 1px solid #CCCCCC;">
		
		<div style="float:left;" class="thread_heading"><?php echo $this->row->slideshows[$k]->name; ?></div>
	
		<div style="float:left; margin-left: 10px;" class="edit">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=meetings&layout=slideshows_form&projectid=".$this->project->id."&meetingid=".$this->row->id."&slideshowid=".$this->row->slideshows[$k]->id); ?>"><?php echo _LANG_EDIT; ?></a>
		</div>
		
		<div style="float:left;" class="delete">
		<a class="delete_slideshow" title="<?php echo $this->row->slideshows[$k]->name; ?>" href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=remove_slideshow&projectid=".$this->project->id."&meetingid=".$this->row->id."&slideshowid=".$this->row->slideshows[$k]->id); ?>"><?php echo _LANG_DELETE; ?></a>
		</div>
		
		<div style="clear:left;"></div>
		
		</div>
		
		<?php if (is_array($this->row->slideshows[$k]->slides) && count($this->row->slideshows[$k]->slides) > 0) : ?>
		<?php foreach ($this->row->slideshows[$k]->slides as $slide) : ?>
		
		<?php
			$lightbox_comment_html = "<div class='comments_info'>
										<a href=''>
											0 Comments
										</a>
										 - 
										<a href=''>
											Post new comment
										</a>
									  </div>";
		?>
		<script>
		$(function() {
			$('.lightbox_<?php echo $this->row->slideshows[$k]->id; ?>').lightBox({fixedNavigation:true});
		});
		</script>
	
		<div class="thumbnail">
		<a class="lightbox_<?php echo $this->row->slideshows[$k]->id; ?>" title="<?php echo $slide->title.$lightbox_comment_html; ?>" href="uploads/projects/<?php echo $this->projectid; ?>/slideshows/<?php echo $slide->slideshowid."/".$slide->filename; ?>">
		<img src="uploads/projects/<?php echo $this->projectid; ?>/slideshows/<?php echo $slide->slideshowid."/thumb/".$slide->filename; ?>" alt="" />
		</a>
		</div>
		
		<?php endforeach; ?>
		
		<div style="clear: left;"></div>
		
		<div style="margin-bottom: 15px;">
		Total slides: <?php echo count($this->row->slideshows[$k]->slides); ?>
		</div>
		<?php endif; ?>
		
	</div>
	<?php endfor; ?>
	<?php else : ?>
	No slideshows.
	<?php endif; ?>
	
	<hr />
	
	<div style="float:left;" class="thread_heading">Files</div>
	
	<div style="float:left; margin-left: 10px;" class="edit">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=meetings&layout=files_form&projectid=".$this->project->id."&meetingid=".$this->row->id); ?>">
	Manage files attached to this meeting
	</a>
	</div>
	
	<div style="clear: left;"></div>
	
	<?php if (is_array($this->row->files) && count($this->row->files) > 0) : ?>
	<table>
	<?php foreach ($this->row->files as $file) : ?>
	<tr>
		<td width="32">
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=download_file&fileid=".$file->id); ?>">
			<img border="0" height="32" width="32" src="templates/<?php echo $this->config->template; ?>/images/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($file->mimetype); ?>" />
			</a>
		</td>
		<td>
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=download_file&fileid=".$file->id); ?>">
			<?php echo $file->title; ?>
			</a>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php else : ?>
	No files.
	<?php endif; ?>
	
	
	<?php if (is_array($this->row->comments) && count($this->row->comments) > 0) : ?>
	<h3><?php echo _LANG_COMMENTS; ?></h3>
	<?php foreach ($this->row->comments as $comment) : ?>
		<div class="comment_row">
			<div style="float:left; margin-right: 10px;">
				<img src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo phpFrame_User_Helper::id2photo($comment->userid); ?>" />
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
		<img src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo !empty($this->settings->photo) ? $this->settings->photo : 'default.png'; ?>" />
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
		<input type="hidden" name="type" value="meetings" />
		<input type="hidden" name="itemid" value="<?php echo  $this->row->id; ?>" />
		<input type="hidden" name="meetingid" value="<?php echo  $this->row->id; ?>" />
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