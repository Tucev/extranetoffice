<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_sys_events
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$sys_events = array(
	"summary" => array("error", "An error occurred while saving project"),
	"events_log" => array(
		array("success", "Project successfully saved."),
		array("error", "Failed to send email to someone@somewhere.com."),
		array("warning", "Some notifications failed.")
	) 
);

?>

<?php if (is_array($sys_events['summary']) && count($sys_events['summary']) > 0) : ?>

<script type="text/javascript">

$(document).ready(function() {
//	window.setTimeout("$(\"#events_summary\").fadeOut(\"slow\")", 3000);
	$("div#events_log").hide();
	$("#events_log").dialog({
		autoOpen: false
	});
	$(".events_summary_error a#more").click(function(){
		$("#events_log").dialog('open');
		$(".events_summary_error a#close").click(function(){
			$(".module_sys_events").hide();
		});
	});
});

</script>

<div id="events_summary">
	<div class="events_summary_<?php echo $sys_events['summary'][0];?>">
		<?php echo $sys_events['summary'][1];?>
		<?php if (is_array($sys_events['events_log']) && count($sys_events['events_log']) > 0) :?>
		<a id="more">more</a> / 
		<?php endif; ?>
				
		<a id="close">close</a>
	</div> 
</div>

<?php if (is_array($sys_events['events_log']) && count($sys_events['events_log']) > 0) :?>
<div id="events_log" title="Event Log">
	<?php foreach ($sys_events['events_log'] as $event) :?>
		<div class="events_summary_<?php echo $event[0];?>">
			<?php echo $event[1]; ?>
		</div>
	<?php endforeach; ?>	
</div>
<?php endif; ?>

<?php endif; ?>