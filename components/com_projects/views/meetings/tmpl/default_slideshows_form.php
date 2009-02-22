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
function submitbutton() {
	var form = document.iofficeform;

	// do field validation
	if (form.name.value == "") {
		alert('<?php echo text::_( _LANG_SLIDESHOWS_NAME_REQUIRED , true); ?>');
		form.name.focus();
		return;
	}
	
	form.submit();
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<form action="index.php" method="post" name="iofficeform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo text::_( _LANG_SLIDESHOWS_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
	<td width="30%">
		<label id="namemsg" for="name">
			<?php echo _LANG_NAME; ?>:
		</label>
	</td>
	<td>
		<input class="inputbox" type="text" id="name" name="name" size="32" maxlength="64" value="<?php echo $this->row->name; ?>" />
	</td>
</tr>
</table>
</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<button class="button" type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button> 	
<button class="button" type="button" onclick="submitbutton();return false;"><?php echo text::_('Save'); ?></button>

<input type="hidden" name="projectid" value="<?php echo $this->projectid;?>" />
<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="task" value="save_meeting" />
<input type="hidden" name="type" value="" />
<?php echo html::_( 'form.token' ); ?>

</form>