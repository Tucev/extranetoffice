<?php
/**
 * src/components/com_dashboard/controller.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_dashboard
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * dashboardController Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_dashboard
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @since      1.0
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
        $projects = $modelProjects->getCollection("p.created", "DESC");
        
        // Get project updates, overdue items and upcoming milestones
        if (count($projects) > 0) {
            foreach ($projects as $project) {
                // Get project updates
                $modelActivitylog = PHPFrame_MVC_Factory::getModel("com_projects", 
                                                                   "activitylog",
                                                                   array($project));
                $project->activitylog = $modelActivitylog->getCollection('ts', 'DESC', 10);
                
                // Get overdue issues
                $modelIssues = PHPFrame_MVC_Factory::getModel("com_projects", 
                                                              "issues", 
                                                              array($project));
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
