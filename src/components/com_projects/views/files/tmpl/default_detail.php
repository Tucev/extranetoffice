<?php
/**
 * src/components/com_projects/views/files/tmpl/default_detail.php
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

// Add confirm behaviour to delete links
PHPFrame_HTML::confirm(
   'delete_file', 
   _LANG_PROJECTS_FILES_DELETE, 
   _LANG_PROJECTS_FILES_DELETE_CONFIRM
);

// Load jQuery validation behaviour for forms
PHPFrame_HTML::validate('commentsform');
?>

<h2 class="componentheading">
    <a href="<?php echo $data['project_url']; ?>">
    <?php echo $data['page_title']; ?>
    </a>
</h2>

<h2 class="subheading <?php echo strtolower($view->getName()); ?>">
    <a href="<?php echo $data["tool_url"]; ?>">
        <?php echo $view->getName(); ?>
    </a>
</h2>


<div class="thread_row0">

    <?php if ($data['row']->userid == PHPFrame::Session()->getUser()->id) : ?>
    <div class="thread_delete">
        <a class="delete_file" title="<?php echo PHPFrame_Base_String::html($data['row']->title, true); ?>" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_file&projectid=".$data['row']->projectid)."&fileid=".$data['row']->id; ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?>
        </a> 
    </div>
    <?php endif; ?>
    <div class="thread_download">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=download_file&fileid=".$data['row']->id); ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DOWNLOAD ); ?>
        </a> 
    </div>
    <div class="thread_upload">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_file_form&projectid='.$data['project']->id."&parentid=".$data['row']->parentid); ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_FILES_UPLOAD_NEW_VERSION ); ?>
        </a> 
    </div>
    
    <div style="float: left; padding: 0 3px 0 0; ">
        <img height="48" width="48" src="themes/<?php echo PHPFrame::Config()->get("THEME"); ?>/images/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($data['row']->mimetype); ?>" alt="<?php echo $data['row']->mimetype; ?>" />
    </div>
    
    <div class="thread_heading">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=download_file&fileid=".$data['row']->id); ?>">
        <?php echo $data['row']->title; ?>
    </a>
    </div>
    
    <div class="thread_date">
    <?php echo date("D, d M Y H:ia", strtotime($data['row']->ts)); ?> 
    </div>
    
    <div class="thread_details">
    <?php echo _LANG_FILES_FILENAME.": ".$data['row']->filename; ?> (Revision <?php echo $data['row']->revision; ?>) 
    Uploaded by: <?php echo $data['row']->created_by_name; ?>
    <br />
    <?php echo projectsViewHelper::printAssignees("files", $data['row']); ?>
    </div>
    
    <?php if (!empty($data['row']->changelog)) : ?>
    <div class="thread_body">
        <?php echo $data['row']->changelog; ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($data['row']->children)) : ?>
    
    <!-- jquery slider for show/hide older versions -->
    <script language="javascript" type="text/javascript">
    $(document).ready(function() {
        // hides the filterpanel as soon as the DOM is ready
        $('#oldrevisions<?php echo $data['row']->id; ?>').hide();
        // toggles the filterpanel on clicking the noted link  
        $('a#toggle<?php echo $data['row']->id; ?>').click(function() {
            $('#oldrevisions<?php echo $data['row']->id; ?>').slideToggle('normal');
            return false;
          });
    });
    </script>
    
    <a class="show_revisions" id="toggle<?php echo $data['row']->id; ?>" href="#">Show/Hide older versions</a>

    <div id="oldrevisions<?php echo $data['row']->id; ?>" class="thread_oldrevisions">
        <?php foreach ($data['row']->children as $child) : ?>
            <div class="thread_oldrevision_entry">
                <?php if ($data['row']->userid == PHPFrame::Session()->getUser()->id) : ?>
                <div class="thread_delete">
                    <a class="delete_file" title="<?php echo PHPFrame_Base_String::html($child->title, true); ?>" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_file&projectid=".$data['row']->projectid)."&fileid=".$child->id; ?>">
                        <?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?>
                    </a> 
                </div>
                <?php endif; ?>
                <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=download_file&fileid=".$child->id); ?>">
                    <?php echo $child->title; ?>
                </a> 
                (Revision <?php echo $child->revision; ?> - <?php echo date("D, d M Y H:ia", strtotime($child->ts)); ?>) 
                Uploaded by: <?php echo $child->created_by_name; ?>
                <br />
                <?php echo $child->changelog; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($data['row']->comments) && $data['row']->comments->countRows() > 0) : ?>
    <h3><?php echo _LANG_COMMENTS; ?></h3>
    <?php foreach ($data['row']->comments as $comment) : ?>
        <div class="comment_row">
            <div style="float:left; margin-right: 10px;">
                <img src="<?php echo PHPFrame::Config()->get("UPLOAD_DIR").'/users/'; ?><?php echo PHPFrame_User_Helper::id2photo($comment->userid); ?>" />
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
    	<?php 
    	$user = PHPFrame::Session()->getUser();
    	$photo = $user->photo;
    	?>
        <img src="<?php echo PHPFrame::Config()->get("UPLOAD_DIR").'/users/'; ?><?php echo !empty($photo) ? $photo : 'default.png'; ?>" />
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
        <button class="button"><?php echo _LANG_COMMENTS_SEND; ?></button>
        </p>
        <input type="hidden" name="component" value="com_projects" />
        <input type="hidden" name="action" value="save_comment" />
        <input type="hidden" name="projectid" value="<?php echo $data['project']->id; ?>" />
        <input type="hidden" name="type" value="files" />
        <input type="hidden" name="itemid" value="<?php echo  $data['row']->id; ?>" />
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
