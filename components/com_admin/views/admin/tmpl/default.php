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
	$(function() {
		$("#tabs").tabs();
	});
</script>

<h2 class="componentheading"><?php echo $this->page_title; ?></h2>

<div id="tabs">
	<ul>
		<li>
			<a href="<?php echo route::_("index.php?option=com_admin&amp;view=config"); ?>">Global Config</a>
		</li>
		<li>
			<a href="<?php echo route::_("index.php?option=com_admin&amp;view=users"); ?>">Users</a>
		</li>
		<li>
			<a href="<?php echo route::_("index.php?option=com_admin&amp;view=components"); ?>">Components</a>
		</li>
		<li>
			<a href="<?php echo route::_("index.php?option=com_admin&amp;view=modules"); ?>">Modules</a>
		</li>
	</ul>
</div>
	
