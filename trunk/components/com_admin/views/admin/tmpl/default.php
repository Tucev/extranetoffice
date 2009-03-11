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
			<a href="index.php?option=com_admin&amp;view=config&tmpl=component">Global Config</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=users&tmpl=component">Users</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=components&tmpl=component">Components</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=modules&tmpl=component">Modules</a>
		</li>
	</ul>
</div>
	
