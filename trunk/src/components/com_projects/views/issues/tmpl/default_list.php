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
phpFrame_HTML::confirm('delete_issue', _LANG_PROJECTS_ISSUES_DELETE, _LANG_PROJECTS_ISSUES_DELETE_CONFIRM);
?>

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
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getView().'&layout=form&projectid='.$this->projectid); ?>">
		<?php echo phpFrame_HTML_Text::_( _LANG_NEW ); ?>
	</a>
</div>

<h2 class="subheading <?php echo strtolower($this->current_tool); ?>">
	<a href="<?php echo phpFrame_Application_Route::_('index.php?component=com_projects&view='.phpFrame_Environment_Request::getView().'&projectid='.$this->projectid); ?>">
		<?php echo $this->current_tool; ?>
	</a>
</h2>


<a id="toggle_filterpannel" href="#">Refine search</a>
<br />
<div id="filterpanel" class="list_filter_container">
	<form name="listsearchform" action="index.php" method="post">
	<table border="0" cellpadding="3" cellspacing="1">
	<tr>
		<td>Search:</td>
		<td><input type="text" name="search" value="<?php echo $this->lists['search']; ?>" /></td>
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
	<input type="hidden" name="component" value="com_projects" />
	<input type="hidden" name="view" value="issues" />
	<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
	</form>
</div>


<?php if (is_array($this->rows) && count($this->rows) > 0) : ?>

<?php $k = 0; ?>
<?php foreach($this->rows as $row) : ?>
<div class="thread_row<?php echo $k; ?>">

	<?php if ($row->created_by == $this->_user->id) : ?>
	<div class="thread_delete">
		<a class="delete_issue" title="<?php echo phpFrame_HTML_Text::_($row->title, true); ?>" href="index.php?component=com_projects&action=remove_issue&projectid=<?php echo $row->projectid; ?>&issueid=<?php echo $row->id; ?>">
			<?php echo phpFrame_HTML_Text::_( _LANG_DELETE ); ?>
		</a> 
	</div>
	<?php endif; ?>
	
	<div class="thread_heading">
	<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&layout=detail&projectid=".$row->projectid."&issueid=".$row->id); ?>">
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
    		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_users&view=users&layout=detail&userid=".$row->assignees[$j]['id']); ?>">
    		<?php echo $row->assignees[$j]['name']; ?>
    		</a>
    	<?php endfor; ?>
    	<?php endif; ?>
    	<br />
    	<?php echo _LANG_DTSTART; ?>: <?php if ($row->dtstart != '0000-00-00') { echo date("d M Y", strtotime($row->dtstart)); } else { echo _LANG_NOT_SET; } ?> 
    	- 
    	<?php echo _LANG_DTEND; ?>: <?php if ($row->dtend != '0000-00-00') { echo date("d M Y", strtotime($row->dtend)); } else { echo _LANG_NOT_SET; } ?>
    	<br />
    	<div class="<?php echo $row->status; ?> status">
    		<span><?php echo $row->status; ?></span>
    	</div>
	</div>
	
	<?php if (!empty($row->description)) : ?>
	<div class="thread_body">
		<?php echo nl2br(phpFrame_HTML_Text::limit_words($row->description, 255)); ?>
	</div>
	<?php endif; ?>
	
	<div class="comments_info">
		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&layout=detail&projectid=".$this->projectid."&issueid=".$row->id); ?>">
			<?php echo $row->comments; ?> <?php echo _LANG_COMMENTS; ?>
		</a>
		 - 
		<a href="<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&view=issues&layout=detail&projectid=".$this->projectid."&issueid=".$row->id."#post-comment"); ?>">
			<?php echo _LANG_COMMENTS_NEW; ?>
		</a>
	</div>

</div>
<?php $k = 1 - $k; ?>
<?php endforeach; ?>

<br />

<form name="pagenavform">
<?php echo $this->pageNav->getListFooter(); ?>
<input type="hidden" name="component" value="com_projects" />
<input type="hidden" name="view" value="projects" />
<input type="hidden" name="type" value="issues" />
<input type="hidden" name="projectid" value="<?php echo $this->projectid; ?>" />
</form>

<?php else : ?>
<?php echo phpFrame_HTML_Text::_( _LANG_NO_ENTRIES ); ?>
<?php endif; ?>

<?php //echo '<pre>'; var_dump($this->rows); echo '</pre>'; ?>