<?php var_dump($data);?>
<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

// Add confirm behaviour to delete links
phpFrame_HTML::confirm('delete_meeting', _LANG_PROJECTS_MEETINGS_DELETE, _LANG_PROJECTS_MEETINGS_DELETE_CONFIRM);
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>


<div class="new">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_meeting_form&layout=form&projectid='.$data['project']->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_meeting_form&layout=form&projectid='.$data['project']->id); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == $this->_user->id) : ?>
	<div class="thread_delete">
		<a class="delete_meeting" title="<?php echo phpFrame_HTML_Text::_($row->name, true); ?>" href="index.php?component=com_projects&action=remove_meeting&projectid=<?php echo $row->projectid; ?>&meetingid=<?php echo $row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getViewName().'&layout=detail&projectid='.$row->projectid.'&meetingid='.$row->id); ?>">
		<?php echo $row->name; ?>
	</a>
	</div>
	
	<div class="thread_date">
	Start: <?php echo date("D, d M Y", strtotime($row->dtstart)); ?>
	</div>
	
	<div class="thread_date">
	End: <?php echo date("D, d M Y", strtotime($row->dtend)); ?>
	</div>
	
	<div class="thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $row->created_by_name; ?><br />
		<?php echo _LANG_ASSIGNEES; ?>: 
		<?php if (!empty($row->assignees)) : ?>
    	<?php for ($j=0; $j<count($row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
	</div>
	
	<?php if (!empty($row->description)) : ?>
	<div class="thread_body">
		<?php echo nl2br(phpFrame_HTML_Text::limit_words($row->description, 255)); ?>
	</div>
	<?php endif; ?>
	
	<div class="comments_info">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=meetings&layout=detail&projectid=".$this->projectid."&meetingid=".$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=meetings&layout=detail&projectid=".$this->projectid."&meetingid=".$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>