<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsViewHelper Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class projectsViewHelper {
	public static function printAssignees($assignees) {
		$str = _LANG_ASSIGNEES.": ";
		
		if (is_array($assignees) && count($assignees) > 0) {
    		for ($j=0; $j<count($assignees); $j++) {
    			// Build URL to user profile
    			$usr_url = "index.php?component=com_users&action=get_user&userid=";
    			$usr_url .= $assignees[$j]['id'];
    			
    			// separate names by commas
    			if ($j>0) $str .= ', ';
    			
    			// Add link with user name to return string
    			$str .= '<a href="';
    			$str .= phpFrame_Utils_Rewrite::rewriteURL($usr_url);
    			$str .= '">';
    			$str .= $assignees[$j]['name'];
    			$str .= '</a>';
    		}
    	}
    	else {
    		$str .= _LANG_NONE;
    	}
    	
    	return $str;
	}
}
