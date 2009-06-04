<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

// Add confirm behaviour to delete links
phpFrame_HTML::confirm('delete_milestone', _LANG_PROJECTS_MILESTONES_DELETE, _LANG_PROJECTS_MILESTONES_DELETE_CONFIRM);
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>


<div class="new">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestone_form&projectid='.$data['project']->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestones&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($data['rows'] as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == phpFrame::getUser()->id) : ?>
	<div class="thread_delete">
		<a class="delete_milestone" title="<?php echo phpFrame_HTML_Text::_($row->title, true); ?>" href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&action=remove_milestone&projectid=".$row->projectid."&milestoneid=".$row->id); ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestone_detail&projectid='.$row->projectid.'&milestoneid='.$row->id); ?>">
		<?php echo $row->title; ?>
	</a>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->created)); ?>
	</div>
	
	<div class="thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $row->created_by_name; ?>
		<br />
		<?php echo projectsViewHelper::printAssignees("milestones", $row); ?>
	</div>
	
	<div class="<?php echo $row->due_date_class; ?> status">
    	<span>
    		<?php echo strtoupper($row->status); ?> &gt; 
			<?php echo _LANG_MILESTONES_DUEDATE; ?>: 
			<?php echo date("l, d M Y", strtotime($row->due_date)); ?>
    	</span>
    </div>
	
	<br />
	
	<?php if (!empty($row->description)) : ?>
	<div class="thread_body">
		<?php echo nl2br(phpFrame_HTML_Text::limit_words($row->description, 255)); ?>
	</div>
	<?php endif; ?>
	
	
	<div class="comments_info">
		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestone_detail&projectid='.$data['project']->id.'&milestoneid='.$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL('index.php?component=com_projects&action=get_milestone_detail&projectid='.$data['project']->id.'&milestoneid='.$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>