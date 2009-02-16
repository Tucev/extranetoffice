<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice.Projects
 * @subpackage 	viewProjects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<div class="ioffice_dashboard_rss">
	<a href="#">
	RSS
	</a>
</div>

<div class="componentheading"><?php echo $this->page_title; ?></div>


<div class="ioffice_dashboard_item">
	<h3 class="overdue_issues"><?php echo _INTRANETOFFICE_ISSUES_OVERDUE; ?></h3>
	
	<?php if (is_array($this->overdue_issues) && count($this->overdue_issues) > 0) : ?>
	<table>
	<?php foreach ($this->overdue_issues as $issue) : ?>
	<tr>
		<td>
			<?php echo date("D, d M Y", strtotime($issue->dtend)); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_intranetoffice&view=projects&type=issues_detail&projectid=".$this->projectid."&issueid=".$issue->id); ?>">
			<?php echo $issue->title; ?>
			</a>
		</td>
		<td>
			<?php echo _INTRANETOFFICE_ASSIGNEES; ?>: 
			<?php if (!empty($issue->assignees)) : ?>
	    	<?php for ($j=0; $j<count($issue->assignees); $j++) : ?>
	    		<?php if ($j>0) echo ', '; ?>
	    		<a href="<?php echo JRoute::_("index.php?option=com_intranetoffice&view=users&type=detail&userid=".$issue->assignees[$j]['id']); ?>">
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

<div class="ioffice_dashboard_item">
	<h3 class="upcoming_milestones"><?php echo _INTRANETOFFICE_MILESTONES_UPCOMING; ?></h3>
</div>

<div class="ioffice_dashboard_item">
	<h3 class="project_updates"><?php echo _INTRANETOFFICE_PROJECTS_UPDATES; ?></h3>
	
	<?php if (is_array($this->activitylog) && count($this->activitylog) > 0) : ?>
	<table>
	<?php foreach ($this->activitylog as $log) : ?>
	<tr>
		<td width="70">
			<div class="activitylog_<?php echo $log->type; ?>">
				<?php echo iOfficeHelperProjects::activitylog_type2printable($log->type); ?>
			</div>
		</td>
		<td>
			<a href="<?php echo JRoute::_($log->url); ?>">
			<?php echo $log->title; ?>
			</a>
		</td>
		<td><?php echo $log->action." by ".iOfficeHelperUsers::id2name($log->userid); ?></td>
		<td><?php echo date("D, d M Y H:ia", strtotime($log->ts)); ?></td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php endif; ?>
	
</div>
	
<?php //echo '<pre>'; var_dump($this->projects); echo '</pre>'; ?>