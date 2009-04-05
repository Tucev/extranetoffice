<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );
?>

<!-- add jQuery tabs behaviour -->
<script type="text/javascript">
var $sysadmin_tabs;
var $sysadmin_selected_tab='ui-tabs-30';

$(function() {
	$sysadmin_tabs = $('#sysadmin_tabs');
	$sysadmin_tabs.tabs();
	$sysadmin_tabs.bind('tabsload', function(event, ui) {
	    $sysadmin_selected_tab = ui.panel.id;
	});
	$sysadmin_tabs.bind('tabsselect', function(event, ui) {
	    $sysadmin_selected_tab = ui.panel.id;
	    $sysadmin_unselected_tabs = $(this).find("div[id^='ui-tabs']:not(div#"+$sysadmin_selected_tab+")");
	    $sysadmin_unselected_tabs.html('');
	});
});
</script>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<div id="sysadmin_tabs">
	<ul>
		<li>
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_admin&amp;view=config"); ?>">Global Config</a>
		</li>
		<li>
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_admin&amp;view=users"); ?>">Users</a>
		</li>
		<li>
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_admin&amp;view=components"); ?>">Components</a>
		</li>
		<li>
			<a href="<?php echo phpFrame_Application_Route::_("index.php?option=com_admin&amp;view=modules"); ?>">Modules</a>
		</li>
	</ul>
</div>
	
