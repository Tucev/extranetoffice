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
phpFrame_HTML::confirm('delete_message', _LANG_PROJECTS_MESSAGES_DELETE, _LANG_PROJECTS_MESSAGES_DELETE_CONFIRM);
?>

<h2 class="componentheading"><?php echo $data['page_heading']; ?></h2>


<div class="new">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_message_form&projectid='.$data['project']->id); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($data['view']); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_messages&projectid='.$data['project']->id); ?>">
		<?php echo $data['view']; ?>
	</a>
</h2>


<?php if (is_array($data['rows']) && count($data['rows']) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($data['rows'] as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->userid == phpFrame::getUser()->id) : ?>
	<div class="thread_delete">
		<a class="delete_message" title="<?php echo phpFrame_HTML_Text::_($row->subject, true); ?>" href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&action=remove_message&projectid=".$row->projectid."&messageid=".$row->id); ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_message_detail&projectid='.$row->projectid.'&messageid='.$row->id); ?>">
		<?php echo $row->subject; ?>
	</a>
	</div>
	
	<div class="thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->date_sent)); ?>
	</div>
	
	<div class="thread_details">
		<?php echo _LANG_POSTED_BY ?>: <?php echo $row->created_by_name; ?><br />
		<?php echo _LANG_ASSIGNEES; ?>: 
		<?php if (!empty($row->assignees)) : ?>
    	<?php for ($j=0; $j<count($row->assignees); $j++) : ?>
    		<?php if ($j>0) echo ', '; ?>
    		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&action=get_user&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
	</div>
	
	<?php if (!empty($row->body)) : ?>
	<div class="thread_body">
		<?php echo nl2br(phpFrame_HTML_Text::limit_words($row->body, 255)); ?>
	</div>
	<?php endif; ?>
	
	<div class="comments_info">
		<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_message_detail&projectid='.$data['project']->id.'&messageid='.$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&action=get_message_detail&projectid='.$data['project']->id.'&messageid='.$row->id.'#post-comment'); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>