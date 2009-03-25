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

<?php if (is_object($this->row)) : ?>

<div class="row_icons">
	
	<a href="<?php echo route::_("index.php?option=com_users&view=users&layout=detail&userid=".$this->row->id); ?>">
	<img border="0" src="<?php echo $this->config->get('upload_dir').'/users/'; ?><?php echo !empty($this->row->photo) ? $this->row->photo : 'default.png'; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo route::_("index.php?option=com_users&view=users&layout=detail&userid=".$this->row->id); ?>">
		<?php echo usersHelper::fullname_format($this->row->firstname, $this->row->lastname); ?>
	</a>
	</div>

</div>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>