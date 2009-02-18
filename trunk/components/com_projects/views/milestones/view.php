<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsViewMilestones Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class projectsViewMilestones extends view {
	var $page_title=null;
	var $projectid=null;
	
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	function displayMilestones() {
		$this->doBreadcrumbs($this->page_subheading);
		
		$modelMilestones = new iOfficeModelMilestones();
		$milestones = $modelMilestones->getMilestones($this->projectid);
		$this->assignRef('rows', $milestones['rows']);
		$this->assignRef('pageNav', $milestones['pageNav']);
		$this->assignRef('lists', $milestones['lists']);
	}
	
	function displayMilestonesDetail() {
		$this->doBreadcrumbs($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));	
	
		$modelMilestones = new iOfficeModelMilestones();
		$milestone = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
		$this->assignRef('row', $milestone);
		
		$this->page_title .= ' - '.$milestone->title;
		$this->doBreadcrumbs($milestone->title);
	}
	
	function displayMilestonesForm() {
		if (!empty($this->milestoneid)) {
			$action = _LANG_MILESTONES_EDIT;
			$modelMilestones = new iOfficeModelMilestones();
			$milestone = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
			$this->assignRef('row', $milestone);
		}
		else {
			$action = _LANG_MILESTONES_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->doBreadcrumbs($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->doBreadcrumbs($action);
	}
}
?>