<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage     com_users
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<?php if (is_object($data['row'])) : ?>

<div class="row_icons">
    
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&view=users&layout=detail&userid=".$data['row']->id); ?>">
    <img border="0" src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($data['row']->photo) ? $data['row']->photo : 'default.png'; ?>" alt="<?php echo $data['row']->firstname." ".$data['row']->lastname; ?>" />
    </a>
    
    <div class="row_icons_heading">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&view=users&layout=detail&userid=".$data['row']->id); ?>" >
        
        <?php $shortlastname = PHPFrame_Base_String::limitChars($data['row']->lastname, 10); ?>
        
        <?php echo PHPFrame_User_Helper::fullname_format($data['row']->firstname, $shortlastname); ?>
    </a>
    </div>

</div>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>