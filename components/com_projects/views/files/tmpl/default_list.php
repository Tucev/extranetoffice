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

<div class="new">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'_form&projectid='.$this->projectid); ?>">
		<?php echo text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo $this->current_tool; ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'&projectid='.$this->projectid); ?>">
		<?php echo $this->page_subheading; ?>
	</a>
</h2>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="ioffice_thread_row<?php echo $k; ?>">

	<?php if ($row->userid == $this->user->id) : ?>
	<div class="ioffice_thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $row->id; ?>, '<?php echo text::_($row->title, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	<div class="ioffice_thread_download">
		<a href="<?php echo route::_("index.php?option=com_projects&task=download_file&fileid=".$row->id); ?>">
			<?php echo text::_( _LANG_DOWNLOAD ); ?>
		</a> 
	</div>
	<div class="ioffice_thread_upload">
		<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout=files_form&projectid='.$this->projectid."&parentid=".$row->parentid); ?>">
			<?php echo text::_( _LANG_FILES_UPLOAD_NEW_VERSION ); ?>
		</a> 
	</div>
	
	<div style="float: left; padding: 0 3px 0 0; ">
		<img height="48" width="48" src="templates/default/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($row->mimetype); ?>" alt="<?php echo $row->mimetype; ?>" />
	</div>
	
	<div class="ioffice_thread_heading">
	<a href="<?php echo route::_("index.php?option=com_projects&task=download_file&fileid=".$row->id); ?>">
		<?php echo $row->title; ?>
	</a>
	</div>
	
	<div class="ioffice_thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->ts)); ?> 
	</div>
	
	<div class="ioffice_thread_details">
	<?php echo _LANG_FILES_FILENAME.": ".$row->filename; ?> (Revision <?php echo $row->revision; ?>) 
	Uploaded by: <?php echo $row->created_by_name; ?>
	</div>
	
	<?php if (!empty($row->changelog)) : ?>
	<div class="ioffice_thread_body">
		<?php echo nl2br(enoiseFormat::limit_words($row->changelog, 255)); ?>
	</div>
	<?php endif; ?>
	
	<?php if (!empty($row->children)) : ?>
	<script language="javascript" type="text/javascript">
	window.addEvent('domready', function() {
		var mySlide = new Fx.Slide('oldrevisions<?php echo $row->id; ?>');
		mySlide.hide();
		
		$('toggle<?php echo $row->id; ?>').addEvent('click', function(e){
			e = new Event(e);
			mySlide.toggle();
			e.stop();
		});
	});
	</script>
	<a class="show_revisions" id="toggle<?php echo $row->id; ?>" href="#">Show/Hide older versions</a>

	<div id="oldrevisions<?php echo $row->id; ?>" class="ioffice_thread_oldrevisions">
		<?php foreach ($row->children as $child) : ?>
			<div class="ioffice_thread_oldrevision_entry">
				<?php if ($row->userid == $this->user->id) : ?>
				<div class="ioffice_thread_delete">
					<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $child->id; ?>, '<?php echo text::_($child->title." r".$child->revision, true); ?>');">
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
				<?php echo nl2br(enoiseFormat::limit_words($child->changelog, 255)); ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<div class="ioffice_files_detail_comments">
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=files_detail&projectid=".$this->projectid."&fileid=".$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=files_detail&projectid=".$this->projectid."&fileid=".$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>
</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_FILES ); ?>
<?php endif; ?>

<pre><?php //var_dump($this->rows); ?></pre>