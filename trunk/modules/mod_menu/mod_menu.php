<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_menu
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$option = request::getVar('option', 'com_dashboard');
$active_component = substr($option, 4);
$db =& factory::getDB();

$query = "SELECT * FROM #__components WHERE system = '0' ORDER BY ordering ASC";
$db->setQuery($query);
$components = $db->loadObjectList();
?>

<ul id="menu">
	<li <?php if ($active_component == 'dashboard') { echo ' class="selected"'; } ?>>
		<a href="index.php?option=com_dashboard">Dashboard</a>
	</li>
	<?php foreach ($components as $component) : ?>
	<li <?php if ($active_component == $component->name) { echo ' class="selected"'; } ?>>
		<a href="index.php?option=com_<?php echo $component->name; ?>"><?php echo $component->menu_name; ?></a>
	</li>
	<?php endforeach; ?>
</ul>