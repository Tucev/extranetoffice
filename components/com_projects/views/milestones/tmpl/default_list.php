<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Add confirm behaviour to delete links
html::confirm('delete_milestone', _LANG_PROJECTS_MILESTONES_DELETE, _LANG_PROJECTS_MILESTONES_DELETE_CONFIRM);
?>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>


<div class="new">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=form&projectid='.$this->projectid); ?>">
		<?php echo text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == $this->user->id) : ?>
	<div class="thread_delete">
		<a class="delete_milestone" title="<?php echo text::_($row->title, true); ?>" href="index.php?option=com_projects&task=remove_milestone&projectid=<?php echo $row->projectid; ?>&milestoneid=<?php echo $row->id; ?>">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$row->projectid.'&milestoneid='.$row->id); ?>">
		<?php echo $row->title; ?>
	</a>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->created)); ?>
	</div>
	
	<div class="thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $row->created_by_name; ?><br />
		<?php echo _LANG_ASSIGNEES; ?>: 
		<?php if (!empty($row->assignees)) : ?>
    	<?php for ($j=0; $j<count($row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo route::_("index.php?option=com_projects&view=users&layout=detail&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
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
		<?php echo nl2br(text::limit_words($row->description, 255)); ?>
	</div>
	<?php endif; ?>
	
	
	<div class="comments_info">
		<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&milestoneid='.$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&milestoneid='.$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>