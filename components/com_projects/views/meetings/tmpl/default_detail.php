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
function confirm_delete(projectid, meetingid, label) {
	var answer = confirm("Are you sure you want to delete meeting '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_meeting&projectid="+projectid+"&meetingid="+meetingid;
	}
}
</script>

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
		<a href="Javascript:confirm_delete(<?php echo $this->row->projectid; ?>, <?php echo $this->row->id; ?>, '<?php echo text::_($this->row->name, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="ioffice_thread_edit">
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=meetings_form&projectid=".$this->project->id."&meetingid=".$this->row->id); ?>">
		<?php echo text::_( _LANG_EDIT ); ?>
		</a>
	</div>
	
	<div class="ioffice_thread_heading">
		<?php echo $this->row->name; ?>
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
    	<?php echo _LANG_DTSTART; ?>: <?php echo date("d M Y", strtotime($this->row->dtstart)); ?><br /> 
    	<?php echo _LANG_DTEND; ?>: <?php echo date("d M Y", strtotime($this->row->dtend)); ?>
	</div>
</div>

<div style="clear: left;"></div>



<h2>Slideshows</h2>

<!-- 
<div>
<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=meetings_slideshows_form&projectid=".$this->project->id."&meetingid=".$this->row->id); ?>">
Add new slideshow
</a>
</div>
 -->
 
<?php if (is_array($this->row->slideshows) && count($this->row->slideshows) > 0) : ?>
<?php for ($k=0; $k<count($this->row->slideshows); $k++) : ?>
<div>
	<h4><?php echo $this->row->slideshows[$k]->name; ?></h4>

	<?php if (is_array($this->row->slideshows[$k]->slides) && count($this->row->slideshows[$k]->slides) > 0) : ?>
	<?php foreach ($this->row->slideshows[$k]->slides as $slide) : ?>
	
	<?php
		$lightbox_comment_html = "<div class='ioffice_files_detail_comments'>
									<a href=''>
										0 Comments
									</a>
									 - 
									<a href=''>
										Post new comment
									</a>
								  </div>";
	?>
	
	<div class="ioffice_thumbnail">
	<a rel="lightbox[<?php echo $slide->slideshowid; ?>]" title="<?php echo $slide->title.$lightbox_comment_html; ?>" href="images/intranetoffice/projects/<?php echo $this->projectid; ?>/slideshows/<?php echo $slide->slideshowid."/".$slide->filename; ?>">
	<img src="images/intranetoffice/projects/<?php echo $this->projectid; ?>/slideshows/<?php echo $slide->slideshowid."/thumb/".$slide->filename; ?>" alt="" />
	</a>
	</div>
	
	<?php endforeach; ?>
	
	<div style="clear: left;"></div>
	
	<br />
	Total slides: <?php echo count($this->row->slideshows[$k]->slides); ?>
	
	<?php endif; ?>
	
</div>
<?php endfor; ?>
<?php else : ?>
No slideshows.
<?php endif; ?>

<h2>Files</h2>

<table>
<tr>
	<td width="32">
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=26">
		<img border="0" height="32" width="32" src="templates/default/icons/mimetypes/32x32/pdf.png" />
		</a>
	</td>
	<td>
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=26">
		StickyWorld CRD 13.01.09 Agenda
		</a>
	</td>
</tr>
<tr>
	<td width="32">
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=25">
		<img border="0" height="32" width="32" src="templates/default/icons/mimetypes/32x32/pdf.png" />
		</a>
	</td>
	<td>
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=25">
		StickyWorld CRD masterprogramme rev2 
		</a>
	</td>
</tr>
<tr>
	<td width="32">
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=24">
		<img border="0" height="32" width="32" src="templates/default/icons/mimetypes/32x32/pdf.png" />
		</a>
	</td>
	<td>
		<a href="index.php?option=com_projects&amp;task=download_file&amp;fileid=24">
		Microsoft Word - StickyWorld CRD Quarter 1Progress report 
		</a>
	</td>
</tr>
</table>

<!-- 
<h2>Polls</h2>
 -->

</div><!-- close #ioffice_main_col -->

<div style="clear: left;"></div>

<?php //echo '<pre>'; var_dump($this->row); echo '</pre>'; ?>