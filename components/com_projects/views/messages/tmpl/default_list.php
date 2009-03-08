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
?>

<script language="javascript" type="text/javascript">
function confirm_delete(projectid, messageid, label) {
	var answer = confirm("Are you sure you want to delete message '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_message&projectid="+projectid+"&messageid="+messageid;
	}
}
</script>

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

	<?php if ($row->userid == $this->user->id) : ?>
	<div class="thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $row->id; ?>, '<?php echo text::_($row->subject, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$row->projectid.'&messageid='.$row->id); ?>">
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
    		<a href="<?php echo route::_("index.php?option=com_projects&view=users&layout=detail&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
	</div>
	
	<?php if (!empty($row->body)) : ?>
	<div class="thread_body">
		<?php echo nl2br(text::limit_words($row->body, 255)); ?>
	</div>
	<?php endif; ?>
	
	<div class="files_detail_comments">
		<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&messageid='.$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo route::_('index.php?option=com_projects&view='.request::getVar('view').'&layout=detail&projectid='.$this->projectid.'&messageid='.$row->id.'#post-comment'); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>