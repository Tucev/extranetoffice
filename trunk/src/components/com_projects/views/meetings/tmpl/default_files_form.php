<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?option=com_projects&view='.phpFrame_Environment_Request::getVar('view').'&projectid='.$this->projectid); ?>">
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
			<?php echo _LANG_FILES; ?>:
		</label>
	</td>
	<td>
		<?php if (is_array($this->project_files) && count($this->project_files) > 0) : ?>
		<?php foreach ($this->project_files as $project_file) : ?>
		<input type="checkbox" name="fileids[<?php echo $project_file->id; ?>]" <?php if (in_array($project_file->id, $this->meeting_files_ids)) { echo 'checked'; } ?> /> 
		<?php echo $project_file->title; ?> <br />
		<?php endforeach; ?>
		<?php endif; ?>
	</td>
</tr>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="window.location = '<?php echo phpFrame_Application_Route::_("index.php?option=com_projects&view=meetings&layout=detail&projectid=".$this->projectid."&meetingid=".$this->meetingid); ?>';"><?php echo phpFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo phpFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
<input type="hidden" name="meetingid" value="<?php echo phpFrame_Environment_Request::getVar('meetingid', 0); ?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_meetings_files" />
<?php echo phpFrame_HTML::_( 'form.token' ); ?>

</form>