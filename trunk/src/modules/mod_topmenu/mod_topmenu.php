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

$user = phpFrame::getUser();
?>
		
<div id="top">
	<span class="icons_16_outer">
	<span class="icons_16 users_16">
		<?php phpFrame_HTML::dialog('Account', 'index.php?component=com_users&action=get_settings', 600, 560, true); ?>
	</span>
	</span>
	
	<?php if ($user->groupid == 1) : ?>
	<span class="icons_16_outer">
	<span class="icons_16 sysadmin_16">
		<?php phpFrame_HTML::dialog('System Admin', 'index.php?component=com_admin', 760, 650); ?>
	</span>
	</span>
	<?php endif; ?>
	
	<span class="icons_16_outer">
	<span class="icons_16 security_16">
		<a href="index.php?component=com_login&amp;action=logout">Logout</a>
	</span>
	</span>
	
	<br />
	
	<div>You are logged on as: <?php echo $user->username; ?></div>
</div>