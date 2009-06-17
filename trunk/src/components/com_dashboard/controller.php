<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage    com_dashboard
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * dashboardController Class
 * 
 * @package        PHPFrame
 * @subpackage     com_dashboard
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 */
class dashboardController extends PHPFrame_MVC_ActionController
{
    /**
     * Constructor
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function __construct()
    {
        // Invoke parent's constructor to set default action
        parent::__construct('get_dashboard');
    }
    
    public function get_dashboard()
    {
        // Get user's projects
        $modelProjects = PHPFrame_MVC_Factory::getModel("com_projects", "projects");
        $projects = $modelProjects->getCollection($list_filter);
        
        // Get project updates, overdue items and upcoming milestones
        if (count($projects) > 0) {
            foreach ($projects as $project) {
                // Get project updates
                $activitylog_filter = new PHPFrame_Database_CollectionFilter('ts', 'DESC', 10);
                $modelActivitylog = PHPFrame_MVC_Factory::getModel("com_projects", "activitylog", array($project));
                $project->activitylog = $modelActivitylog->getCollection($activitylog_filter);
                
                // Get overdue issues
                $modelIssues = PHPFrame_MVC_Factory::getModel("com_projects", "issues");
                $project->overdue_issues = $modelIssues->getTotalIssues($project->id, true);
            }
        }
        
        // Get view
        $view = $this->getView('dashboard');
        // Set view data
        $view->addData('projects', $projects);
        // Display view
        $view->display();
    }
}
