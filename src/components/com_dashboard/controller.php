<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_dashboard
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * dashboardController Class
 * 
 * @package		phpFrame
 * @subpackage 	com_dashboard
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class dashboardController extends phpFrame_Application_ActionController {
	/**
	 * Constructor
	 * 
	 * @return	void
	 */
	protected function __construct() {
		// Invoke parent's constructor to set default action and default view
		parent::__construct('get_dashboard');
	}
	
	public function get_dashboard() {
		// Create list filter needed for getProjects()
		$list_filter = new phpFrame_Database_Listfilter('p.name', 'ASC');
		
		// Get user's projects
		$modelProjects = phpFrame::getModel("com_projects", "projects");
		$projects = $modelProjects->getProjects($list_filter, phpFrame::getUser()->id);
		
		// Get project updates, overdue items and upcoming milestones
		if (is_array($projects) && count($projects) > 0) {
			foreach ($projects as $row) {
				// Get project updates
				$modelActivitylog = phpFrame::getModel("com_projects", "activitylog");
				$row->activitylog = $modelActivitylog->getActivityLog($row->id);
				
				// Get overdue issues
				$modelIssues = phpFrame::getModel("com_projects", "issues");
				$row->overdue_issues = $modelIssues->getTotalIssues($row->id, true);
			}
		}
		
		// Get view
		$view = $this->getView('dashboard', '');
		// Set view data
		$view->addData('projects', $projects);
		// Display view
		$view->display();
	}
}
?>