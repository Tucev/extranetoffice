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
function confirm_delete(projectid, meetingid, label) {
	var answer = confirm("Are you sure you want to delete meeting '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_meeting&projectid="+projectid+"&meetingid="+meetingid;
	}
}
</script>

<h2 class="componentheading"><?php echo $this->page_heading; ?></h2>


<div class="new">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'_form&projectid='.$this->projectid); ?>">
		<?php echo text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo $this->current_tool; ?>">
	<a href="<?php echo route::_('index.php?option=com_projects&view=projects&layout='.$this->current_tool.'&projectid='.$this->projectid); ?>">
		<?php echo $this->page_subheading; ?>
	</a>
</h2>

<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="ioffice_thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == $this->user->id) : ?>
	<div class="ioffice_thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $row->id; ?>, '<?php echo text::_($row->name, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="ioffice_thread_heading">
	<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=meetings_detail&projectid=".$row->projectid."&meetingid=".$row->id); ?>">
		<?php echo $row->name; ?>
	</a>
	</div>
	
	<div class="ioffice_thread_date">
	Start: <?php echo date("D, d M Y", strtotime($row->dtstart)); ?>
	</div>
	
	<div class="ioffice_thread_date">
	End: <?php echo date("D, d M Y", strtotime($row->dtend)); ?>
	</div>
	
	<div class="ioffice_thread_details">
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
	
	<?php if (!empty($row->description)) : ?>
	<div class="ioffice_thread_body">
		<?php echo nl2br($row->description); ?>
	</div>
	<?php endif; ?>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<?php else : ?>
<?php echo text::_( _LANG_NO_MESSAGES ); ?>
<?php endif; ?>