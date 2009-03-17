<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php foreach($this->rows as $row) : ?>
<div class="row_icons">
	
	<a href="<?php echo route::_("index.php?option=com_users&view=users&layout=detail&userid=".$row->id); ?>">
	<img border="0" src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo !empty($row->photo) ? $row->photo : 'default.png'; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo route::_("index.php?option=com_users&view=users&layout=detail&userid=".$row->id); ?>">
		<?php echo usersHelper::fullname_format($row->firstname, $row->lastname); ?>
	</a>
	</div>

</div>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>