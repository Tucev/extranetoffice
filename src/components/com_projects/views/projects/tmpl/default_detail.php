<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<div class="rss_top_right">
	<a href="#">
	RSS
	</a>
</div>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>


<div class="main_col_module">
	<h3 class="overdue_issues"><?php echo _LANG_ISSUES_OVERDUE; ?></h3>
	
	<?php if (is_array($data['overdue_issues']) && count($data['overdue_issues']) > 0) : ?>
	<table>
	<?php foreach ($data['overdue_issues'] as $issue) : ?>
	<tr>
		<td>
			<?php echo date("D, d M Y", strtotime($issue->dtend)); ?>
		</td>
		<td>
			<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=get_issue_detail&projectid=".$issue->projectid."&issueid=".$issue->id); ?>">
			<?php echo $issue->title; ?>
			</a>
		</td>
		<td>
			<?php echo _LANG_ASSIGNEES; ?>: 
			<?php if (!empty($issue->assignees)) : ?>
	    	<?php for ($j=0; $j<count($issue->assignees); $j++) : ?>
	    		<?php if ($j>0) echo ', '; ?>
	    		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users&action=get_user&userid=".$issue->assignees[$j]['id']); ?>">
	    		<?php echo $issue->assignees[$j]['name']; ?>
	    		</a>
	    	<?php endfor; ?>
	    	<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php endif; ?>
	
</div>

<div class="main_col_module">
	<h3 class="upcoming_milestones"><?php echo _LANG_MILESTONES_UPCOMING; ?></h3>
</div>

<div class="main_col_module">
	<h3 class="project_updates"><?php echo _LANG_PROJECTS_UPDATES; ?></h3>
	
	<?php if (count($data['activitylog']) > 0) : ?>
	<table>
	<?php foreach ($data['activitylog'] as $log) : ?>
	<tr>
		<td width="70">
			<div class="activitylog_<?php echo $log->type; ?>">
				<?php echo projectsHelperProjects::activitylog_type2printable($log->type); ?>
			</div>
		</td>
		<td>
			<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL($log->url); ?>">
			<?php echo $log->title; ?>
			</a>
		</td>
		<td><?php echo $log->action." by ".phpFrame_User_Helper::id2name($log->userid); ?></td>
		<td><?php echo date("D, d M Y H:ia", strtotime($log->ts)); ?></td>
		<td>
		<?php if ($data['roleid'] == 1 || $log->userid == phpFrame::getUser()->id) : ?>
			<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_activitylog&projectid=".$log->projectid."&id=".$log->id); ?>">
			Delete
			</a>
		<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php endif; ?>
	
</div>
	
<?php //echo '<pre>'; var_dump($data); echo '</pre>'; ?>