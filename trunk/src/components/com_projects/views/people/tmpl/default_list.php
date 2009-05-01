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
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getViewName().'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php foreach($this->rows as $row) : ?>
<div class="row_icons">
	
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$row->id); ?>">
	<img border="0" src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($row->photo) ? $row->photo : 'default.png'; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$row->id); ?>">
		<?php echo $row->name; ?>
	</a>
	</div>

</div>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>