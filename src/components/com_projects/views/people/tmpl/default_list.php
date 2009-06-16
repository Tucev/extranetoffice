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
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_people&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>

<?php foreach($data['rows'] as $row) : ?>
<div class="row_icons">
	
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$row->id); ?>">
	<img border="0" src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($row->photo) ? $row->photo : 'default.png'; ?>" alt="<?php echo $row->firstname." ".$row->lastname; ?>" />
	</a>
	
	<div class="row_icons_heading">
	<a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$row->id); ?>" title="<?php echo $row->firstname." ".$row->lastname; ?>">
	
		<?php $shortlastname = PHPFrame_Base_String::limitChars($row->lastname, 10); ?>
		
		<?php echo PHPFrame_User_Helper::fullname_format($row->firstname, $shortlastname); ?>
	</a>
	</div>
</div>

<?php endforeach; ?>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>