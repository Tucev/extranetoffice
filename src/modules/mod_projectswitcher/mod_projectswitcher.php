<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage 	mod_projectswitcher
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

$projectid = phpFrame_Environment_Request::getVar('projectid', 0);
require_once _ABS_PATH.DS.'components'.DS.'com_projects'.DS.'helpers'.DS.'projects.helper.php';
?>

<!-- Project switcher -->
<script language="javascript" type="text/javascript">
function switch_project(projectid) {
	if (projectid > 0) {
		window.location = "<?php echo phpFrame_Application_Route::_("index.php?component=com_projects&action=get_project_detail"); ?>&projectid="+projectid;
	}
}
</script>

<div id="project_switcher">
<?php echo _LANG_PROJECTS_SWITCHER; ?>&nbsp;&nbsp; 
<?php echo projectsHelperProjects::select($projectid, 'onchange="Javascript:switch_project(this.options[selectedIndex].value);"'); ?>
</div>