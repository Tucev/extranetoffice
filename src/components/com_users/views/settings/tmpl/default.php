<?php
/**
 * src/components/com_users/views/settings/tmpl/default.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

PHPFrame_HTML::validate('userform');
?>


<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<form action="index.php" method="post" name="userform" id="userform" enctype="multipart/form-data">
    
<fieldset>
<legend><?php echo PHPFrame_Base_String::html( _LANG_USER_GENERAL_SETTINGS ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
    <td>
        <label for="username">
            <?php echo PHPFrame_Base_String::html( _LANG_USERNAME ); ?>:
        </label>
    </td>
    <td>
        <input class="required" type="text" id="username" name="username" value="<?php echo $data['row']->get('username');?>" size="40" />
    </td>
</tr>
<tr>
    <td width="120">
        <label for="email">
            <?php echo PHPFrame_Base_String::html( _LANG_EMAIL ); ?>:
        </label>
    </td>
    <td>
        <input class="required" type="text" id="email" name="email" value="<?php echo $data['row']->get('email');?>" size="40" />
    </td>
</tr>
<tr>
    <td>
        <label for="firstname">
            <?php echo PHPFrame_Base_String::html( _LANG_FIRSTNAME ); ?>:
        </label>
    </td>
    <td>
        <input type="text" id="firstname" name="firstname" value="<?php echo $data['row']->get('firstname');?>" size="40" />
    </td>
</tr>
<tr>
    <td>
        <label for="lastname">
            <?php echo PHPFrame_Base_String::html( _LANG_LASTNAME ); ?>:
        </label>
    </td>
    <td>
        <input type="text" id="lastname" name="lastname" value="<?php echo $data['row']->get('lastname');?>" size="40" />
    </td>
</tr>
<tr>
    <td>
        <label for="password">
            <?php echo PHPFrame_Base_String::html( _LANG_PASSWORD ); ?>:
        </label>
    </td>
    <td>
        <input type="password" id="password" name="password" value="" size="40" />
    </td>
</tr>
<tr>
    <td>
        <label for="password2">
            <?php echo PHPFrame_Base_String::html( _LANG_PASSWORD_VERIFY ); ?>:
        </label>
    </td>
    <td>
        <input type="password" id="password2" name="password2" size="40" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="photomsg" for="photo">
            <?php echo _LANG_USER_PHOTO; ?>:
        </label>
    </td>
    <td>
        <?php if (is_string($data['row']->photo)) : ?>
            <img src="<?php echo PHPFrame::Config()->get("UPLOAD_DIR").'/users/'.$data['row']->photo; ?>" alt="photo" vspace="5" />
            <br />
        <?php endif; ?>
        <input type="file" name="photo" id="photo" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="notificationsmsg" for="notifications">
            <?php echo _LANG_USER_EMAIL_NOTIFICATIONS; ?>:
        </label>
    </td>
    <td>
        <select id="notifications" name="notifications">
            <option value="0" <?php if ($data['row']->notifications == 0) echo 'selected'; ?>>No</option>
            <option value="1" <?php if ($data['row']->notifications == 1) echo 'selected'; ?>>Yes</option>
        </select>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="show_emailmsg" for="show_email">
            <?php echo _LANG_USER_SHOW_EMAIL; ?>:
        </label>
    </td>
    <td>
        <select id="show_email" name="show_email">
            <option value="0" <?php if ($data['row']->show_email == 0) echo 'selected'; ?>>No</option>
            <option value="1" <?php if ($data['row']->show_email == 1) echo 'selected'; ?>>Yes</option>
        </select>
    </td>
</tr>
</table>
</fieldset>

<div style="clear:both; margin-top:30px;"></div>

<?php if (PHPFrame::Request()->get('tmpl') != 'component') : ?>
<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button>
<?php endif; ?>

<input type="hidden" name="id" value="<?php echo $data['row']->get('id'); ?>" />
<input type="hidden" name="groupid" value="<?php echo $data['row']->get('groupid');?>" />
<input type="hidden" name="component" value="com_users" />
<input type="hidden" name="action" value="save_user" />
<input type="hidden" name="ret_url" value="<?php echo $data['ret_url']; ?>">
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>

<?php //var_dump($data); exit; ?>