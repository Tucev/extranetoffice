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

$tmpl = request::getVar('tmpl', '');
if (!empty($tmpl)) {
	$tmpl = '&tmpl='.$tmpl;
}
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
			<a href="index.php?option=com_admin&amp;view=config<?php echo $tmpl; ?>">Global Config</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=users<?php echo $tmpl; ?>">Users</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=components<?php echo $tmpl; ?>">Components</a>
		</li>
		<li>
			<a href="index.php?option=com_admin&amp;view=modules<?php echo $tmpl; ?>">Modules</a>
		</li>
	</ul>
</div>
	
