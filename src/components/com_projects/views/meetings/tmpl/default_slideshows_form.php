<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Load jQuery validation behaviour for form
phpFrame_HTML::validate('slideshowsform');
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getView().'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<form action="index.php" method="post" id="slideshowsform" name="slideshowsform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo phpFrame_HTML_Text::_( $this->action ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="required" type="text" id="name" name="name" size="32" maxlength="64" value="<?php echo $this->row->name; ?>" />
	</td>
</tr>
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_SLIDES; ?>:
		</label>
	</td>
	<td>
	<?php if (empty($this->row->id)) : ?>
		Please save the slideshow before adding slides by using the "Save button below"
	<?php else : ?>
	
		<?php phpFrame_HTML::upload(array('option'=>'com_projects', 
								 'task'=>'upload_slide', 
								 'projectid'=>$this->projectid, 
								 'meetingid'=>$this->row->meetingid, 
								 'slideshowid'=>$this->row->id),
						   'filename',
						   '$("div.thumbnail:last").after("\
						   <div class=\"thumbnail\">\
						   <img src=\"uploads/projects/'.$this->projectid.'/slideshows/'.$this->row->id.'/thumb/"+file+"\" alt=\"\" />\
						   <br />\
						   <a href=\"'.phpFrame_Application_Route::_("index.php?component=com_projects&action=remove_slide&projectid=".$this->projectid."&meetingid=".$this->row->meetingid."&slideshowid=".$this->row->id."&slideid=\"+response+\"").'\">Delete</a>\
						   </div>");'
		); ?>
		<hr />
		<?php if (is_array($this->row->slides) && count($this->row->slides) > 0) : ?>
		<?php foreach ($this->row->slides as $slide) : ?>
		<div class="thumbnail">
		<img src="uploads/projects/<?php echo $this->projectid; ?>/slideshows/<?php echo $slide->slideshowid."/thumb/".$slide->filename; ?>" alt="" />
		<br />
		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&action=remove_slide&projectid=".$this->projectid."&meetingid=".$this->row->meetingid."&slideshowid=".$slide->slideshowid."&slideid=".$slide->id); ?>">Delete</a>
		</div>
		<?php endforeach; ?>
		<?php else : ?>
		<div class="thumbnail" style="visibility: hidden; position: absolute;"></div>
		<?php endif; ?>
	
	<?php endif; ?>
	</td>
</tr>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="window.location = '<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=meetings&layout=detail&projectid=".$this->projectid."&meetingid=".$this->meetingid); ?>';"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
<input type="hidden" name="meetingid" value="<?php echo phpFrame_Environment_Request::getVar('meetingid', 0); ?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_slideshow" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>