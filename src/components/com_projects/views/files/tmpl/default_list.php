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
phpFrame_HTML::confirm('delete_file', _LANG_PROJECTS_FILES_DELETE, _LANG_PROJECTS_FILES_DELETE_CONFIRM);
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<div class="new">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&layout=form&projectid='.$this->projectid); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->userid == $this->user->id) : ?>
	<div class="thread_delete">
		<a class="delete_file" title="<?php echo phpFrame_HTML_Text::_($row->title, true); ?>" href="index.php?option=com_projects&task=remove_file&projectid=<?php echo $row->projectid; ?>&fileid=<?php echo $row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	<div class="thread_download">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=download_file&fileid=".$row->id); ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DOWNLOAD ); ?>
		</a> 
	</div>
	<div class="thread_upload">
		<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&layout=form&projectid='.$this->projectid."&parentid=".$row->parentid); ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_FILES_UPLOAD_NEW_VERSION ); ?>
		</a> 
	</div>
	
	<div style="float: left; padding: 0 3px 0 0; ">
		<img height="48" width="48" src="templates/<?php echo config::TEMPLATE; ?>/images/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($row->mimetype); ?>" alt="<?php echo $row->mimetype; ?>" />
	</div>
	
	<div class="thread_heading">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=download_file&fileid=".$row->id); ?>">
		<?php echo $row->title; ?>
	</a>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->ts)); ?> 
	</div>
	
	<div class="thread_details">
	<?php echo _LANG_FILES_FILENAME.": ".$row->filename; ?> (Revision <?php echo $row->revision; ?>) 
	Uploaded by: <?php echo $row->created_by_name; ?>
	<?php if (!empty($row->assignees)) : ?>
		<br />
		<?php echo _LANG_ASSIGNEES; ?>: 
    	<?php for ($j=0; $j<count($row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=users&layout=detail&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    <?php endif; ?>
    	
	</div>
	
	<?php if (!empty($row->changelog)) : ?>
	<div class="thread_body">
		<?php echo nl2br(phpFrame_HTML_Text::limit_words($row->changelog, 255)); ?>
	</div>
	<?php endif; ?>
	
	<?php if (!empty($row->children)) : ?>
	
	<!-- jquery slider for show/hide older versions -->
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		// hides the filterpanel as soon as the DOM is ready
		$('#oldrevisions<?php echo $row->id; ?>').hide();
		// toggles the filterpanel on clicking the noted link  
		$('a#toggle<?php echo $row->id; ?>').click(function() {
			$('#oldrevisions<?php echo $row->id; ?>').slideToggle('normal');
			return false;
  		});
	});
	</script>
		
	<a class="show_revisions" id="toggle<?php echo $row->id; ?>" href="#">Show/Hide older versions</a>

	<div id="oldrevisions<?php echo $row->id; ?>" class="thread_oldrevisions">
		<?php foreach ($row->children as $child) : ?>
			<div class="thread_oldrevision_entry">
				<?php if ($row->userid == $this->user->id) : ?>
				<div class="thread_delete">
					<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $child->id; ?>, '<?php echo phpFrame_HTML_Text::_($child->title." r".$child->revision, true); ?>');">
						<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
					</a> 
				</div>
				<?php endif; ?>
				<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&task=download_file&fileid=".$child->id); ?>">
					<?php echo $child->title; ?>
				</a> 
				(Revision <?php echo $child->revision; ?> - <?php echo date("D, d M Y H:ia", strtotime($child->ts)); ?>) 
				Uploaded by: <?php echo $child->created_by_name; ?>
				<br />
				<?php echo nl2br(phpFrame_HTML_Text::limit_words($child->changelog, 255)); ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<div class="comments_info">
		<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&fileid='.$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&fileid='.$row->id.'#post-comment'); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>
</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>

<pre><?php //var_dump($this->rows); ?></pre>