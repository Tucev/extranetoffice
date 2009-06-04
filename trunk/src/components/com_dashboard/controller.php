<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_dashboard
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

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
		// Invoke parent's constructor to set default action
		parent::__construct('get_dashboard');
	}
	
	public function get_dashboard() {
		// Create list filter needed for getProjects()
		$list_filter = new phpFrame_Database_CollectionFilter('p.name', 'ASC');
		
		// Get user's projects
		$modelProjects = phpFrame::getModel("com_projects", "projects");
		$projects = $modelProjects->getCollection($list_filter);
		
		// Get project updates, overdue items and upcoming milestones
		if (count($projects) > 0) {
			foreach ($projects as $project) {
				// Get project updates
				$activitylog_filter = new phpFrame_Database_CollectionFilter('ts', 'DESC', 10);
				$modelActivitylog = phpFrame::getModel("com_projects", "activitylog", array($project));
				$project->activitylog = $modelActivitylog->getCollection($activitylog_filter);
				
				// Get overdue issues
				$modelIssues = phpFrame::getModel("com_projects", "issues");
				$project->overdue_issues = $modelIssues->getTotalIssues($project->id, true);
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