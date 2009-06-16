<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * projectsViewHelper Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsViewHelper {
	public static function printAssignees($tool, $row) {
		$str = _LANG_ASSIGNEES.": ";
		
		if (is_array($row->assignees) && count($row->assignees) > 0) {
    		for ($j=0; $j<count($row->assignees); $j++) {
    			// Build URL to user profile
    			$usr_url = "index.php?component=com_users&action=get_user&userid=";
    			$usr_url .= $row->assignees[$j]['id'];
    			
    			// separate names by commas
    			if ($j>0) $str .= ', ';
    			
    			// Add link with user name to return string
    			$str .= '<a href="';
    			$str .= PHPFrame_Utils_Rewrite::rewriteURL($usr_url);
    			$str .= '">';
    			$str .= $row->assignees[$j]['name'];
    			$str .= '</a>';
    		}
    	}
    	else {
    		$str .= _LANG_NONE;
    	}
    	
    	echo $str;
    	
    	// Add option to change assignees if user is creator or project admin
    	// First we get the projectsModelPermissions
    	/*
    	$controller = PHPFrame_MVC_ActionController::getInstance('com_projects');
    	$project = $controller->project;
    	$project_permissions = projectsModelPermissions::getInstance();
    	if ($project_permissions->getRoleId() || $created_by == $project_permissions->getUserId()) {
    		$form_url = "index.php?component=com_projects&action=get_assignees_form&projectid=".$project->id;
    		$form_url .= "&tool=".$tool."&itemid=".$itemid;
    		PHPFrame_HTML::dialog(_LANG_EDIT, $form_url, 650, 450, true);
    	}
    	*/
	}
}
