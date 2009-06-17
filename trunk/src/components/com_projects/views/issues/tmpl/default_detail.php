<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

// Add confirm behaviour to delete links
PHPFrame_HTML::confirm('delete_issue', _LANG_PROJECTS_ISSUES_DELETE, _LANG_PROJECTS_ISSUES_DELETE_CONFIRM);
// Load jQuery validation behaviour for forms
PHPFrame_HTML::validate('commentsform');
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_issues&projectid='.$data['project']->id); ?>">
        <?php echo $data['view']; ?>
    </a>
</h2>

<div class="thread_row0">
    
    <?php if ($data['row']->created_by == PHPFrame::Session()->getUser()->id) : ?>
    <div class="thread_delete">
        <a class="delete_issue" title="<?php echo PHPFrame_Base_String::html($data['row']->title, true); ?>" href="index.php?component=com_projects&action=remove_issue&projectid=<?php echo $data['row']->projectid; ?>&issueid=<?php echo $data['row']->id; ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?>
        </a> 
    </div>
    <?php endif; ?>
    
    <div class="thread_edit">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_issue_form&projectid=".$data['project']->id."&issueid=".$data['row']->id); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_EDIT ); ?>
        </a>
    </div>
    
    <?php if ($data['row']->closed == "0000-00-00 00:00:00") : ?>
    <div class="thread_close">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=close_issue&projectid=".$data['project']->id."&issueid=".$data['row']->id); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_ISSUES_CLOSE ); ?>
        </a>
    </div>
    <?php else : ?>
    <div class="thread_reopen">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=reopen_issue&projectid=".$data['project']->id."&issueid=".$data['row']->id); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_ISSUES_REOPEN ); ?>
        </a>
    </div>
    <?php endif; ?>
    
    <div class="thread_heading">
        <?php echo $data['row']->title; ?>
    </div>
    
    <div class="thread_date">
    <?php echo date("D, d M Y H:ia", strtotime($data['row']->created)); ?>
    </div>
    
    <div class="thread_details">
        <?php echo _LANG_POSTED_BY ?>: <?php echo $data['row']->created_by_name; ?>
        <br />
        <?php echo projectsViewHelper::printAssignees("issues", $data['row']); ?>
        <br />
        <?php echo _LANG_DTSTART; ?>: <?php if ($data['row']->dtstart != '0000-00-00') { echo date("d M Y", strtotime($data['row']->dtstart)); } else { echo _LANG_NOT_SET; } ?> 
        - 
        <?php echo _LANG_DTEND; ?>: <?php if ($data['row']->dtend != '0000-00-00') { echo date("d M Y", strtotime($data['row']->dtend)); } else { echo _LANG_NOT_SET; } ?>
    </div>
    
    <?php if (!empty($data['row']->description)) : ?>
    <div class="thread_body">
        <?php echo nl2br($data['row']->description); ?>
    </div>
    <?php endif; ?>
    
    <?php if (is_array($data['row']->comments) && count($data['row']->comments) > 0) : ?>
    <h3><?php echo _LANG_COMMENTS; ?></h3>
    <?php foreach ($data['row']->comments as $comment) : ?>
        <div class="comment_row">
            <div style="float:left; margin-right: 10px;">
                <img src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo PHPFrame_User_Helper::id2photo($comment->userid); ?>" />
            </div>
            <div style="margin-left: 95px;">
                <div class="comment_details">
                    <?php echo $comment->created_by_name; ?> &nbsp;&nbsp;
                    <?php echo date("D, d M Y H:ia", strtotime($comment->created)); ?>
                </div>
                <?php echo nl2br($comment->body); ?>
            </div>
        </div>
        <div style="clear: left; margin-bottom: 10px;"></div>
    <?php endforeach; ?>
    <?php endif; ?>
    
</div>

<div>
    <div style="float:left; margin-right: 10px;">
        <img src="<?php echo config::UPLOAD_DIR.'/users/'; ?><?php echo !empty($this->settings->photo) ? $this->settings->photo : 'default.png'; ?>" />
    </div>
    <div style="margin-left: 95px;">
        <form action="index.php" method="post" id="commentsform">
        <a id="post-comment"></a>
        <p><?php echo _LANG_COMMENTS_NEW; ?>:</p>
        <textarea class="required" name="body" rows="10" cols="60"></textarea>
        <p>
        <?php echo _LANG_NOTIFY_ASSIGNEES; ?>: <input type="checkbox" name="notify" checked />
        </p>
        <p>
        <?php echo _LANG_ISSUES_CLOSE; ?>: <input type="checkbox" name="close_issue" />
        </p>
        <p>
        <button class="button"><?php echo _LANG_COMMENTS_SEND; ?></button>
        </p>
        <input type="hidden" name="component" value="com_projects" />
        <input type="hidden" name="action" value="save_comment" />
        <input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
        <input type="hidden" name="type" value="issues" />
        <input type="hidden" name="itemid" value="<?php echo  $data['row']->id; ?>" />
        <input type="hidden" name="issueid" value="<?php echo  $data['row']->id; ?>" />
        <?php if (is_array($data['row']->assignees) && count($data['row']->assignees) > 0) : ?>
        <?php foreach ($data['row']->assignees as $assignee) : ?>
        <input type="hidden" name="assignees[]" value="<?php echo $assignee['id']; ?>" />
        <?php endforeach; ?>
        <?php endif; ?>
        <?php echo PHPFrame_HTML::_( 'form.token' ); ?>
        </form>
    </div>
</div>

<div style="clear: left;"></div>

<?php //echo '<pre>'; var_dump($this->row); echo '</pre>'; ?>