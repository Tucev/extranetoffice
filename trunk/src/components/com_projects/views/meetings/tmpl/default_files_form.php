<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_meetings&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<form action="index.php" method="post" id="slideshowsform" name="slideshowsform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo PHPFrame_HTML_Text::_( $data['action'] ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_FILES; ?>:
		</label>
	</td>
	<td>
		<?php if (is_array($data['project_files']) && count($data['project_files']) > 0) : ?>
		<?php foreach ($data['project_files'] as $project_file) : ?>
		<input type="checkbox" name="fileids[<?php echo $project_file->id; ?>]" <?php if (in_array($project_file->id, $data['meeting_files_ids'])) { echo 'checked'; } ?> /> 
		<?php echo $project_file->title; ?> <br />
		<?php endforeach; ?>
		<?php endif; ?>
	</td>
</tr>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="window.location = '<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_meeting_detail&projectid=".$data['project']->id."&meetingid=".$data['meetingid']); ?>';"><?php echo PHPFrame_HTML_Text::_( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_HTML_Text::_(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
<input type="hidden" name="meetingid" value="<?php echo $data['meetingid']; ?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_meetings_files" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>