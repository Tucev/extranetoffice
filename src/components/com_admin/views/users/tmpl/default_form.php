<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage     com_admin
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

PHPFrame_HTML::validate('usersform');
?>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<form action="index.php" method="post" id="usersform" name="usersform">

    <fieldset>
        <legend><?php echo PHPFrame_Base_String::html( _LANG_ADMIN_USER_DETAILS ); ?></legend>
        
        <table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
        <tr>
            <td width="22%"><?php echo PHPFrame_Base_String::html( _LANG_USERNAME ); ?></td>
            <td><input class="required" type="text" size="30" name="username" value="<?php echo $data['row']->username; ?>" /></td>
        </tr>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_EMAIL ); ?></td>
            <td><input class="required email" type="text" size="30" name="email" value="<?php echo $data['row']->email; ?>" /></td>
        </tr>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_FIRSTNAME ); ?></td>
            <td><input class="required" type="text" size="30" name="firstname" value="<?php echo $data['row']->firstname; ?>" /></td>
        </tr>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_LASTNAME ); ?></td>
            <td><input class="required" type="text" size="30" name="lastname" value="<?php echo $data['row']->lastname; ?>" /></td>
        </tr>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_GROUP ); ?></td>
            <td><?php echo PHPFrame_User_Helper::selectGroup($data['row']->groupid); ?></td>
        </tr>
        <?php if (!empty($data['row']->id)) :?>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_PASSWORD ); ?></td>
            <td><input type="password" size="30" name="password" id="password" value="" /></td>
        </tr>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_PASSWORD_VERIFY ); ?></td>
            <td><input type="password" size="30" name="password2" id="password2" value="" /></td>
        </tr>
        <?php else : ?>
        <tr>
            <td><?php echo PHPFrame_Base_String::html( _LANG_PASSWORD ); ?></td>
            <td><?php echo PHPFrame_Base_String::html( _LANG_PASSWORD_AUTOGEN_INFO ); ?></td>
        </tr>
        <?php endif; ?>
        </table>
    </fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (PHPFrame::Request()->get('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $data['row']->id;?>" />
<input type="hidden" name="component" value="com_admin" />
<input type="hidden" name="action" value="save_user" />
<input type="hidden" name="tmpl" value="<?php echo PHPFrame::Request()->get('tmpl', ''); ?>" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>
</form>