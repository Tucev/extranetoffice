<?php
/**
 * @version		$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_topmenu
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$user =& factory::getUser();
?>
		
<div id="top">
	<span class="icons_16_outer">
	<span class="icons_16 users_16">
		<?php html::dialog('Account', 'index.php?option=com_users&view=settings', 600, 560, true); ?>
	</span>
	</span>
	
	<?php if ($user->groupid == 1) : ?>
	<span class="icons_16_outer">
	<span class="icons_16 sysadmin_16">
		<?php html::dialog('System Admin', 'index.php?option=com_admin', 760, 650); ?>
	</span>
	</span>
	<?php endif; ?>
	
	<span class="icons_16_outer">
	<span class="icons_16 security_16">
		<a href="index.php?option=com_login&amp;task=logout">Logout</a>
	</span>
	</span>
</div>