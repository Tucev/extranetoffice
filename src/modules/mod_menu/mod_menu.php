<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_menu
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

$option = phpFrame::getRequest()->getComponentName();
$active_component = substr($option, 4);
$db = phpFrame::getDB();

$query = "SELECT * FROM #__components WHERE system = '0' AND enabled = '1' ORDER BY ordering ASC";
$db->setQuery($query);
$components = $db->loadObjectList();

$permissions = phpFrame::getPermissions();
?>

<ul id="menu">
	<li <?php if ($active_component == 'dashboard') { echo ' class="selected"'; } ?>>
		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_dashboard"); ?>">Dashboard</a>
	</li>
	<?php foreach ($components as $component) : ?>
	<?php if ($permissions->authorise('com_'.$component->name, '', phpFrame::getSession()->getGroupId())) : ?>
	<li <?php if ($active_component == $component->name) { echo ' class="selected"'; } ?>>
		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_".$component->name); ?>">
			<?php echo $component->menu_name; ?>
		</a>
	</li>
	<?php endif; ?>
	<?php endforeach; ?>
	<li <?php if ($active_component == 'users') { echo ' class="selected"'; } ?>>
		<a href="<?php echo phpFrame_Utils_Rewrite::rewriteURL("index.php?component=com_users"); ?>">Users</a>
	</li>
</ul>