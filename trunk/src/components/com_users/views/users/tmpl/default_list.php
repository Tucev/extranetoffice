<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage 	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>

<?php foreach($data['rows'] as $row) : ?>
<div class="row_icons">
	
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$row->id); ?>">
	<img border="0" src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($row->photo) ? $row->photo : 'default.png'; ?>" alt="<?php echo $row->firstname." ".$row->lastname; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$row->id); ?>" title="<?php echo $row->firstname." ".$row->lastname; ?>"> 
	
	 	<?php $shortlastname = phpFrame_HTML_Text::limit_chars($row->lastname, 10); ?>
	 
	 	<?php echo phpFrame_User_Helper::fullname_format($row->firstname, $shortlastname); ?>
		
	</a>
	</div>

</div>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>