<?php
/**
 * src/components/com_projects/views/files/tmpl/default_list.php
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
PHPFrame_HTML::confirm('delete_file', 
                       _LANG_PROJECTS_FILES_DELETE, 
                       _LANG_PROJECTS_FILES_DELETE_CONFIRM
               );
?>

<h2 class="componentheading">
    <a href="<?php echo $data['project_url']; ?>">
    <?php echo $data['page_title']; ?>
    </a>
</h2>

<div class="new">
    <a href="<?php echo $data["new_file_url"]; ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_NEW ); ?>
    </a>
</div>

<h2 class="subheading <?php echo strtolower($view->getName()); ?>">
    <a href="<?php echo $data["tool_url"]; ?>">
        <?php echo $view->getName(); ?>
    </a>
</h2>


<?php if (isset($data['rows']) && $data['rows']->countRows() > 0) : ?>

<?php $k = 0; ?>
<?php foreach($data['rows'] as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

    <?php if ($row->userid == PHPFrame::Session()->getUser()->id) : ?>
    <div class="thread_delete">
        <a class="delete_file" title="<?php echo PHPFrame_Base_String::html($row->title, true); ?>" href="index.php?component=com_projects&action=remove_file&projectid=<?php echo $row->projectid; ?>&fileid=<?php echo $row->id; ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DELETE ); ?>
        </a> 
    </div>
    <?php endif; ?>
    <div class="thread_download">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=download_file&fileid=".$row->id); ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_DOWNLOAD ); ?>
        </a> 
    </div>
    <div class="thread_upload">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_file_form&projectid='.$data['project']->id."&parentid=".$row->parentid); ?>">
            <?php echo PHPFrame_Base_String::html( _LANG_FILES_UPLOAD_NEW_VERSION ); ?>
        </a> 
    </div>
    
    <div style="float: left; padding: 0 3px 0 0; ">
        <img height="48" width="48" src="themes/<?php echo config::THEME; ?>/images/icons/mimetypes/32x32/<?php echo projectsHelperProjects::mimetype2icon($row->mimetype); ?>" alt="<?php echo $row->mimetype; ?>" />
    </div>
    
    <div class="thread_heading">
    <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=download_file&fileid=".$row->id); ?>">
        <?php echo $row->title; ?>
    </a>
    </div>
    
    <div class="thread_date">
    <?php echo date("D, d M Y H:ia", strtotime($row->ts)); ?> 
    </div>
    
    <div class="thread_details">
    <?php echo _LANG_FILES_FILENAME.": ".$row->filename; ?> (Revision <?php echo $row->revision; ?>) 
    Uploaded by: <?php echo $row->created_by_name; ?>
    <br />
    <?php echo projectsViewHelper::printAssignees("files", $row); ?>
    </div>
    
    <?php if (!empty($row->changelog)) : ?>
    <div class="thread_body">
        <?php echo nl2br(PHPFrame_Base_String::limitWords($row->changelog, 255)); ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($row->children)) : ?>
    
    <!-- jquery slider for show/hide older versions -->
    <script language="javascript" type="text/javascript">
    $(document).ready(function() {
        // hides the filterpanel as soon as the DOM is ready
        $('#oldrevisions<?php echo $row->id; ?>').hide();
        // toggles the filterpanel on clicking the noted link  
        $('a#toggle<?php echo $row->id; ?>').click(function() {
            $('#oldrevisions<?php echo $row->id; ?>').slideToggle('normal');
            return false;
          });
    });
    </script>
        
    <a class="show_revisions" id="toggle<?php echo $row->id; ?>" href="#">Show/Hide older versions</a>

    <div id="oldrevisions<?php echo $row->id; ?>" class="thread_oldrevisions">
        <?php foreach ($row->children as $child) : ?>
            <div class="thread_oldrevision_entry">
                <?php if ($row->userid == PHPFrame::Session()->getUser()->id) : ?>
                <div class="thread_delete">
                    <a class="delete_file" title="<?php echo PHPFrame_Base_String::html($child->title, true); ?>" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_file&projectid=".$data['project']->id)."&fileid=".$child->id; ?>">
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
                <?php echo nl2br(PHPFrame_Base_String::limitWords($child->changelog, 255)); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="comments_info">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_file_detail&projectid='.$data['project']->id.'&fileid='.$row->id); ?>">
            <?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
        </a>
         - 
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_file_detail&projectid='.$data['project']->id.'&fileid='.$row->id.'#post-comment'); ?>">
            <?php echo _LANG_COMMENTS_NEW; ?>
        </a>
    </div>
</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo PHPFrame_Base_String::html( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>
