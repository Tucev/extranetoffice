<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

// Load jQuery validation behaviour for form
PHPFrame_HTML::validate('messagesform');
?>

<h2 class="componentheading">
    <a href="<?php echo $data['project_url']?>">
    <?php echo $data['page_title']; ?>
    </a>
</h2>

<h2 class="subheading <?php echo strtolower($view->getName()); ?>">
    <a href="<?php echo $data["tool_url"]; ?>">
        <?php echo $view->getName(); ?>
    </a>
</h2>

<form action="index.php" method="post" name="messagesform" id="messagesform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo PHPFrame_Base_String::html( _LANG_MESSAGES_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
    <td width="30%">
        <label id="subjectmsg" for="subject">
            <?php echo _LANG_MESSAGES_SUBJECT; ?>:
        </label>
    </td>
    <td>
        <input class="required" type="text" id="subject" name="subject" size="32" maxlength="64" value="" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="bodymsg" for="body">
            <?php echo _LANG_MESSAGES_BODY; ?>:
        </label>
    </td>
    <td>
        <textarea class="required" id="body" name="body" cols="80" rows="10"></textarea>
    </td>
</tr>

<tr>
    <td>
        <label id="assigneesmsg" for="assignees">
            <?php echo _LANG_ASSIGNEES; ?>:
        </label>
    </td>
    <td>
        <?php echo PHPFrame_User_Helper::assignees('', '', 'assignees[]', $data['project']->id); ?>
    </td>
</tr>
<tr>
    <td>
        <label id="notifymsg" for="notify">
            <?php echo _LANG_NOTIFY_ASSIGNEES; ?>:
        </label>
    </td>
    <td>
        <input type="checkbox" name="notify" checked />
    </td>
</tr>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button> 

<input type="hidden" name="projectid" value="<?php echo $data['project']->id;?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_message" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>