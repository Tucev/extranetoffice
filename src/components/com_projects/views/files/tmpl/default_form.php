<?php
/**
 * src/components/com_projects/views/files/tmpl/default_form.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
 */

// Load jQuery validation behaviour for form
PHPFrame_HTML::validate('filesform');
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


<form action="index.php" method="post" id="filesform" name="filesform" enctype="multipart/form-data">

<fieldset>
<legend><?php echo PHPFrame_Base_String::html( _LANG_FILES_NEW ); ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<?php if (isset($data['row'])) : ?>
<tr>
    <td width="30%">
        <label id="parentidmsg" for="parentid">
            <?php echo _LANG_FILES_NEW_VERSION_OF; ?>:
        </label>
    </td>
    <td>
        <?php echo $data['row']->title; ?>
        <input type="hidden" id="parentid" name="parentid" value="<?php echo $data['row']->id; ?>" />
    </td>
</tr>
<?php endif; ?>

<tr>
    <td width="30%">
        <label id="titlemsg" for="title">
            <?php echo _LANG_TITLE; ?>:
        </label>
    </td>
    <td>
        <input class="required" type="text" id="title" name="title" size="32" maxlength="64" value="<?php if (isset($data['row'])) echo $data['row']->title; ?>" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="filenamemsg" for="filename">
            <?php echo _LANG_FILES_FILENAME; ?>:
        </label>
    </td>
    <td>
        <input class="required" type="file" id="filename" name="filename" size="32" maxlength="128" value="" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="changelogmsg" for="changelog">
            <?php echo _LANG_FILES_CHANGELOG; ?>:
        </label>
    </td>
    <td>
        <textarea id="changelog" name="changelog" cols="80"></textarea> 
    </td>
</tr>

<tr>
    <td>
        <label id="assigneesmsg" for="assignees">
            <?php echo _LANG_ASSIGNEES; ?>:
        </label>
    </td>
    <td>
        <?php
        if (isset($data['row'])) {
            $assignees = $data['row']->assignees;
        } else {
            $assignees = null;
        }
        
        echo PHPFrame_User_Helper::assignees(
                                       $assignees, 
                                       '', 
                                       'assignees[]', 
                                       $data["project"]->id
                                   ); 
        ?>
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
<input type="hidden" name="action" value="save_file" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>
