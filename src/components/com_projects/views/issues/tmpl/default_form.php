<?php
/**
 * src/components/com_projects/views/issues/tmpl/default_form.php
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
PHPFrame_HTML::validate('issuesform');
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

<form action="index.php" method="post" id="issuesform" name="issuesform">

<fieldset>
<legend><?php echo empty($data['row']->issueid) ? _LANG_ISSUES_NEW : _LANG_ISSUES_EDIT; ?></legend>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit">
<tr>
    <td width="30%">
        <label id="titlemsg" for="title">
            <?php echo _LANG_NAME; ?>:
        </label>
    </td>
    <td>
        <input class="required" type="text" id="title" name="title" size="32" maxlength="128" value="<?php echo htmlentities($data['row']->title); ?>" />
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="descriptionmsg" for="description">
            <?php echo _LANG_DESCRIPTION; ?>:
        </label>
    </td>
    <td>
        <textarea id="description" name="description" cols="80"><?php echo $data['row']->description; ?></textarea> 
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="issue_typemsg" for="issue_type">
            <?php echo _LANG_ISSUES_TYPE; ?>:
        </label>
    </td>
    <td>
        <?php
        //TODO: Needs Table name eo_issue_types in projectsHelper.php, function name is issue_type_select. 
         echo projectsHelperProjects::issue_type_select($data['row']->issue_type); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="prioritymsg" for="priority">
            <?php echo _LANG_PROJECTS_PRIORITY; ?>:
        </label>
    </td>
    <td>
        <?php echo projectsHelperProjects::priority_select($data['row']->priority); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="dtstartmsg" for="dtstart">
            <?php echo _LANG_DTSTART; ?>:
        </label>
    </td>
    <td>
        <?php echo PHPFrame_HTML::_('calendar', 'dtstart', 'dtstart', $data['row']->dtstart, 'dd/mm/yy', array('size'=>'10',  'maxlength'=>'10')); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="dtendmsg" for="dtend">
            <?php echo _LANG_DTEND; ?>:
        </label>
    </td>
    <td>
        <?php echo PHPFrame_HTML::_('calendar', 'dtend', 'dtend', $data['row']->dtend, 'dd/mm/yy', array('size'=>'10',  'maxlength'=>'10')); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="expected_durationmsg" for="expected_duration">
            <?php echo _LANG_ISSUES_EXPECTED_DURATION; ?>:
        </label>
    </td>
    <td>
        <input class="number" type="text" id="expected_duration" name="expected_duration" size="9" maxlength="9" value="<?php echo $data['row']->expected_duration; ?>" /> 
        <?php echo strtolower(_LANG_HOURS); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="accessmsg" for="access">
            <?php echo _LANG_PROJECTS_ACCESS; ?>:
        </label>
    </td>
    <td>
        <?php echo projectsHelperProjects::global_access_select('access', $data['row']->access); ?>
    </td>
</tr>
<tr>
    <td>
        <label id="assigneesmsg" for="assignees">
            <?php echo _LANG_ASSIGNEES; ?>:
        </label>
    </td>
    <td>
        <?php echo PHPFrame_User_Helper::assignees($data['row']->assignees, '', 'assignees[]', $data['project']->id); ?>
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

<?php if (!empty($data['row']->id)) : ?>
<tr>
    <td width="30%">
        <label id="created_bymsg" for="created_by">
            <?php echo _LANG_CREATED_BY; ?>:
        </label>
    </td>
    <td>
        <?php echo PHPFrame_User_Helper::id2name($data['row']->created_by); ?>
    </td>
</tr>
<tr>
    <td width="30%">
        <label id="createdmsg" for="created">
            <?php echo _LANG_CREATED; ?>:
        </label>
    </td>
    <td>
        <?php echo $data['row']->created; ?>
    </td>
</tr>
<?php endif; ?>
</table>
</fieldset>

<div style="clear:left; margin-top:30px;"></div>

<button type="button" onclick="Javascript:window.history.back();"><?php echo PHPFrame_Base_String::html( _LANG_BACK ); ?></button>
<button type="submit"><?php echo PHPFrame_Base_String::html(_LANG_SAVE); ?></button>

<input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
<input type="hidden" name="id" value="<?php echo $data['row']->id; ?>" />
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="action" value="save_issue" />
<?php echo PHPFrame_HTML::_( 'form.token' ); ?>

</form>