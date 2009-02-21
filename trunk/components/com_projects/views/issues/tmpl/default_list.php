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
function confirm_delete(projectid, issueid, label) {
	var answer = confirm("Are you sure you want to delete issue '"+label+"'?")
	if (answer){
		window.location = "index.php?option=com_projects&task=remove_issue&projectid="+projectid+"&issueid="+issueid;
	}
}
</script>

<!-- jquery slider for filter panel -->
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	// hides the filterpanel as soon as the DOM is ready
	$('#filterpanel.list_filter_container').hide();
	// toggles the filterpanel on clicking the noted link  
	$('a#toggle_filterpannel').click(function() {
		$('#filterpanel.list_filter_container').slideToggle('normal');
		return false;
  	});
});
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


<a id="toggle_filterpannel" href="#">Refine search</a>

<div id="filterpanel" class="list_filter_container">
	<form name="listsearchform" action="index.php" method="post">
	<table border="0" cellpadding="3" cellspacing="1">
	<tr>
		<td>Search:</td>
		<td><input type="text" name="search" class="inputbox" value="<?php echo $this->lists['search']; ?>" /></td>
	</tr>
	<tr>
		<td>Filter:</td>
		<td>
			<select name="filter_status">
				<option value="open" <?php if ($this->lists['status'] == "open") { echo 'selected'; } ?>>Open</option>
				<option value="closed" <?php if ($this->lists['status'] == "closed") { echo 'selected'; } ?>>Closed</option>
				<option value="all" <?php if ($this->lists['status'] == "all") { echo 'selected'; } ?>>All</option>
			</select> 
			
			<select name="filter_assignees">
				<option value="me" <?php if ($this->lists['assignees'] == "me") { echo 'selected'; } ?>>Assigned to me</option>
				<option value="all" <?php if ($this->lists['assignees'] == "all") { echo 'selected'; } ?>>All</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Order by:</td>
		<td>
			<select name="filter_order">
				<option value="i.title" <?php if ($this->lists['order'] == "i.title") { echo 'selected'; } ?>>Title</option>
				<option value="i.dtstart" <?php if ($this->lists['order'] == "i.dtstart") { echo 'selected'; } ?>>Date Start</option>
				<option value="i.dtend" <?php if ($this->lists['order'] == "i.dtend") { echo 'selected'; } ?>>Date End</option>
			</select>
			
			<select name="filter_order_Dir">
				<option value="ASC" <?php if ($this->lists['order_Dir'] == "ASC") { echo 'selected'; } ?>>ASC</option>
				<option value="DESC" <?php if ($this->lists['order_Dir'] == "DESC") { echo 'selected'; } ?>>DESC</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<button class="button">Search</button>
			<button class="button" onclick="document.listsearchform.search.value = '';">Reset</button>
		</td>
	</tr>
	</table>
	<input type="hidden" name="option" value="com_projects" />
	<input type="hidden" name="view" value="projects" />
	<input type="hidden" name="type" value="issues" />
	<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
	</form>
</div>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="ioffice_thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == $this->user->id) : ?>
	<div class="ioffice_thread_delete">
		<a href="Javascript:confirm_delete(<?php echo $row->projectid; ?>, <?php echo $row->id; ?>, '<?php echo text::_($row->subject, true); ?>');">
			<?php echo text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="ioffice_thread_heading">
	<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=issues_detail&projectid=".$row->projectid."&issueid=".$row->id); ?>">
		<?php echo $row->title; ?>
	</a>
	</div>
	
	<div class="ioffice_thread_date">
	<?php echo date("D, d M Y H:ia", strtotime($row->created)); ?>
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
    	<br />
    	<?php echo _LANG_DTSTART; ?>: <?php echo date("d M Y", strtotime($row->dtstart)); ?> 
    	<?php echo _LANG_DTEND; ?>: <?php echo date("d M Y", strtotime($row->dtend)); ?>
    	<br />
    	<div class="ioffice_<?php echo $row->status; ?> ioffice_status">
    		<span><?php echo $row->status; ?></span>
    	</div>
	</div>
	
	<?php if (!empty($row->description)) : ?>
	<div class="ioffice_thread_body">
		<?php echo nl2br(text::limit_words($row->description, 255)); ?>
	</div>
	<?php endif; ?>
	
	<div class="ioffice_files_detail_comments">
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=issues_detail&projectid=".$this->projectid."&issueid=".$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo route::_("index.php?option=com_projects&view=projects&layout=issues_detail&projectid=".$this->projectid."&issueid=".$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<br />

<form name="pagenavform">
<?php echo $this->pageNav->getListFooter(); ?>
<input type="hidden" name="option" value="com_projects" />
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="type" value="issues" />
<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
</form>

<?php else : ?>
<?php echo text::_( _LANG_NO_ISSUES ); ?>
<?php endif; ?>

<?php //echo '<pre>'; var_dump($this->rows); echo '</pre>'; ?>