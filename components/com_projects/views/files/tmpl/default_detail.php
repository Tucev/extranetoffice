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

<script language="javascript" type="text/javascript">
function confirm_delete(projectid, fileid, label) {
	var answer = confirm("Are you sure you want to delete file '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_file&projectid="+projectid+"&fileid="+fileid;
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<div id="right_col">
	<?php require($this->template_path.DS.'menu.html.php'); ?>
</div><!-- close .right_col -->

<div id="main_col">

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<div class="thread_row0">

	<?php if ($this->row->userid == $this->user->id) : ?>
	<div class="thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $this->row->projectid; ?>, <?php echo $this->row->id; ?>, '<?php echo text::_($this->row->title, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	<div class="thread_download">
		<a href="<?php echo route::_("index.php?option=com_projects&task=download_file&fileid=".$this->row->id); ?>">
			<?php echo text::_( _LANG_DOWNLOAD ); ?>
		</a> 
	</div>
	<div class="thread_upload">
		<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout=files_form&projectid='.$this->projectid."&parentid=".$this->row->parentid); ?>">
			<?php echo text::_( _LANG_FILES_UPLOAD_NEW_VERSION ); ?>
		</a> 
	</div>
	
	<div style="float: left; padding: 0 3px 0 0; ">
		<img height="48" width="48" src="templates/<?php echo $this->config->template; ?>/images/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($this->row->mimetype); ?>" alt="<?php echo $this->row->mimetype; ?>" />
	</div>
	
	<div class="thread_heading">
	<a href="<?php echo route::_("index.php?option=com_projects&task=download_file&fileid=".$this->row->id); ?>">
		<?php echo $this->row->title; ?>
	</a>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($this->row->ts)); ?> 
	</div>
	
	<div class="thread_details">
	<?php echo _LANG_FILES_FILENAME.": ".$this->row->filename; ?> (Revision <?php echo $this->row->revision; ?>) 
	Uploaded by: <?php echo $this->row->created_by_name; ?>
	</div>
	
	<?php if (!empty($this->row->changelog)) : ?>
	<div class="thread_body">
		<?php echo $this->row->changelog; ?>
	</div>
	<?php endif; ?>
	
	<?php if (!empty($this->row->children)) : ?>
	<script language="javascript" type="text/javascript">
	window.addEvent('domready', function() {
		var mySlide = new Fx.Slide('oldrevisions<?php echo $this->row->id; ?>');
		mySlide.hide();
		
		$('toggle<?php echo $this->row->id; ?>').addEvent('click', function(e){
			e = new Event(e);
			mySlide.toggle();
			e.stop();
		});
	});
	</script>
	<a class="show_revisions" id="toggle<?php echo $this->row->id; ?>" href="#">Show/Hide older versions</a>

	<div id="oldrevisions<?php echo $this->row->id; ?>" class="thread_oldrevisions">
		<?php foreach ($this->row->children as $child) : ?>
			<div class="thread_oldrevision_entry">
				<?php if ($this->row->userid == $this->user->id) : ?>
				<div class="thread_delete">
					<a href="Javascript:confirm_delete(<?php echo $this->row->projectid; ?>, <?php echo $child->id; ?>, '<?php echo text::_($child->title." r".$child->revision, true); ?>');">
						<?php echo text::_( _LANG_DELETE ); ?>
					</a> 
				</div>
				<?php endif; ?>
				<a href="<?php echo route::_("index.php?option=com_projects&task=download_file&fileid=".$child->id); ?>">
					<?php echo $child->title; ?>
				</a> 
				(Revision <?php echo $child->revision; ?> - <?php echo date("D, d M Y H:ia", strtotime($child->ts)); ?>) 
				Uploaded by: <?php echo $child->created_by_name; ?>
				<br />
				<?php echo $child->changelog; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php if (is_array($this->row->comments) && count($this->row->comments) > 0) : ?>
	<h3><?php echo _LANG_COMMENTS; ?></h3>
	<?php foreach ($this->row->comments as $comment) : ?>
		<div class="comment_row">
			<div style="float:left; margin-right: 10px;">
				<img src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo usersHelperUsers::id2photo($comment->userid); ?>" />
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
		<form method="post">
		<a id="post-comment"></a>
		<p><?php echo _LANG_COMMENTS_NEW; ?>:</p>
		<textarea class="inputbox" name="body" rows="10" cols="60"></textarea>
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
		</form>
	</div>
</div>

</div><!-- close #main_col -->

<div style="clear: left;"></div>

<?php //echo '<pre>'; var_dump($this->row); echo '</pre>'; ?>