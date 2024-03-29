<?php
/**
 * src/components/com_dashboard/views/dashboard/tmpl/default.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_dashboard
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */
?>

<div class="rss_top_right">
    <a href="#">
    RSS
    </a>
</div>

<h2 class="componentheading"><?php echo $data['page_title']; ?></h2>

<div id="right_col">
    
    <div style="float:right;">
        <?php 
            $img = '<img style="float:right; margin: 5px 0 3px 3px;" src="' ;
            $img .= PHPFrame::Config()->get("UPLOAD_DIR")."/users/".PHPFrame::Session()->getUser()->photo . '" />'; 
            PHPFrame_HTML::dialog($img, 'index.php?component=com_users&action=get_settings', 600, 560, true);
        ?> 
    </div>
    
    <h3>My profile</h3>
    
    <?php //var_dump($this->_user); ?>
    
    <p style="font-size: 0.8em;">
    Name: <br /> <?php echo PHPFrame::Session()->getUser()->firstname; ?> <?php echo PHPFrame::Session()->getUser()->lastname; ?> <br />
    Username: <br /> <?php echo PHPFrame::Session()->getUser()->username; ?> <br />
    Email: <br /> <?php echo PHPFrame::Session()->getUser()->email; ?>
    </p>
    
    <div style="clear:right;"></div>
    
    <br />
    
    <?php //if ($this->iOfficeConfig->get('enable_projects')) : ?>
    
    <div class="ioffice_module">
    
    <?php if (PHPFrame::Session()->getUser()->groupid == 1) : ?>
    <div style="float:right;" class="new">
         <a style="float:right;" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_form"); ?>" title="<?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_NEW ); ?>">
        <?php echo PHPFrame_Base_String::html( _LANG_PROJECTS_NEW ); ?>
        </a>
    </div>
    <?php endif; ?>
    
    <h3><?php echo _LANG_DASHBOARD_MY_PROJECTS;  ?></h3>
    <ul>
        <?php if ($data['projects']->countRows() > 0) : ?>
        <?php foreach ($data['projects'] as $project) : ?>
        <li>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_detail&projectid=".$project->id); ?>">
                <?php echo $project->name; ?>
            </a>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    
    </div><!-- close .ioffice_module -->
    
    <?php //endif; // enable projects? ?>
    
</div><!-- close .ioffice_right_col -->

<div id="main_col_2">

<?php //if ($this->iOfficeConfig->get('enable_email_client') && $this->settings->enable_email_client) : ?>
<!-- 
<div class="main_col_module">
    
    <h3 class="recent_email"><?php echo _LANG_DASHBOARD_RECENT_EMAILS; ?></h3>
    
    <?php if (is_array($this->emails) && count($this->emails) > 0) : ?>
    <table border="0" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
    <th></th>
    <th><?php echo _LANG_EMAIL_FROM; ?></th>
    <th><?php echo _LANG_EMAIL_SUBJECT; ?></th>
    <th><?php echo _LANG_EMAIL_DATE; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php $k = 0; ?>
    <?php foreach ($this->emails as $email) : ?>
    <?php 
      if ($email->answered == 1) { 
          $status_icon = '<img src="templates/'.PHPFrame::Config()->get("THEME").'/images/icons/email/replied.png" alt="Unread" />';
      }
      elseif ($email->seen == 0) {
          $status_icon = '<img src="templates/'.PHPFrame::Config()->get("THEME").'/images/icons/email/new.png" alt="Unread" />';
      }
      else { 
          $status_icon = '<img src="templates/'.PHPFrame::Config()->get("THEME").'/images/icons/email/read.png" alt="Read" />'; 
      }
    ?>
    <tr class="row<?php echo $k; ?> seen<?php echo $email->seen; ?> <?php if ($email->deleted == 1) echo 'deleted'; ?>">
    <td>
      <?php echo $status_icon; ?> 
      <?php echo $attachment_icon; ?>
      </td>
    <td>
        <a class="bold" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=messages&layout=detail&folder=INBOX&uid=".$email->uid); ?>">
        <?php echo substr($email->from, 0, 32); if (strlen($email->from) > 33) { echo '...'; } ?>
        </a>
    </td>
    <td>
        <a class="bold" href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_email&view=messages&layout=detail&folder=INBOX&uid=".$email->uid); ?>">
        <?php echo substr($email->subject, 0, 32); if (strlen($email->subject) > 33) { echo '...'; } ?>
        </a>
    </td>
    <td>
        <?php echo date("d M Y H:i:s", strtotime($email->date)); ?>
    </td>
    </tr>
    <?php $k = 1 - $k; ?>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?php elseif ($this->emails) : ?>
    <?php echo PHPFrame_Base_String::html( _LANG_NO_EMAIL ); ?>
    <?php else : ?>
    No e-mail account.
    <?php endif; ?>
    
</div>
 -->
<?php //endif; // enable email client? ?>

<?php //if ($this->config->get('enable_projects')) : ?>
    
<div class="main_col_module">
    <h3 class="project_updates"><?php echo _LANG_PROJECTS_UPDATES; ?></h3>
    
    <?php if (count($data['projects']) > 0) : ?>
    <?php foreach ($data['projects'] as $project) : ?>
    
    <?php if ($project->activitylog->countRows() > 0) : ?>
    <h4>
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_project_detail&projectid=".$project->id); ?>">
        <?php echo $project->name; ?>
        </a>
    </h4>
    
    <div class="overdue_issues_16">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_issues&projectid=".$project->id); ?>">
        <?php echo $project->overdue_issues." "._LANG_ISSUES_OVERDUE; ?>
        </a>
    </div>
    <div class="upcoming_milestones_16">
        <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_milestones&projectid=".$project->id); ?>">
        <?php echo _LANG_MILESTONES_UPCOMING; ?>
        </a>
    </div>
    <br />
    
    <table>
    <?php foreach ($project->activitylog as $log) : ?>
    <tr>
        <td width="70">
            <div class="activitylog_<?php echo $log->type; ?>">
                <?php echo projectsHelperProjects::activitylog_type2printable($log->type); ?>
            </div>
        </td>
        <td>
            <a href="<?php echo PHPFrame_Utils_Rewrite::rewriteURL($log->url); ?>">
            <?php echo $log->title; ?>
            </a>
        </td>
        <td><?php echo $log->action." by ".PHPFrame_User_Helper::id2name($log->userid); ?></td>
        <td><?php echo date("D, d M Y H:ia", strtotime($log->ts)); ?></td>
    </tr>
    <?php endforeach; ?>
    </table>
    <br />
    <?php endif; ?>
    
    <?php endforeach; ?>
    <?php endif; ?>
        
</div>

<?php //endif; // enabled projects? ?>
    
</div>