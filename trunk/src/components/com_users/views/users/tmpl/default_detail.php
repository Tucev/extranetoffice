<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage 	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<?php if (is_object($data['row'])) : ?>

<div class="row_icons">
	
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$data['row']->id); ?>">
	<img border="0" src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($data['row']->photo) ? $data['row']->photo : 'default.png'; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$data['row']->id); ?>">
		<?php echo phpFrame_User_Helper::fullname_format($data['row']->firstname, $data['row']->lastname); ?>
	</a>
	</div>

</div>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>