<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_dashboard
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * dashboardViewDashboard Class
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
 * @subpackage 	com_dashboard
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class dashboardViewDashboard extends view {
	var $page_title=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Override view display method
	 * 
	 * This method overrides the parent display() method and appends the page title to the document title.
	 * 
	 * @todo	This method needs to be re-written to treat dashboard items as modules.
	 * @return	void
	 * @since	1.0
	 */
	function display() {
		$this->page_title = _LANG_DASHBOARD;
		
		// Get user's projects
		require_once _ABS_PATH.DS.'components'.DS.'com_projects'.DS.'models'.DS.'projects.php';
		require_once _ABS_PATH.DS.'components'.DS.'com_projects'.DS.'models'.DS.'activitylog.php';
		require_once _ABS_PATH.DS.'components'.DS.'com_projects'.DS.'models'.DS.'issues.php';
		require_once _ABS_PATH.DS.'components'.DS.'com_projects'.DS.'helpers'.DS.'projects.helper.php';
		$modelProjects =& phpFrame::getInstance('projectsModelProjects');
		$projects = $modelProjects->getProjects(0, $this->user->id);
		// Get project updates, overdue items and upcoming milestones
		if (is_array($projects['rows']) && count($projects['rows']) > 0) {
			foreach ($projects['rows'] as $row) {
				// Get project updates
				$modelActivitylog =& phpFrame::getInstance('projectsModelActivitylog');
				$row->activitylog = $modelActivitylog->getActivityLog($row->id);
				
				// Get overdue issues
				$modelIssues =& phpFrame::getInstance('projectsModelIssues');
				$row->overdue_issues = $modelIssues->getTotalIssues($row->id, true);
			}
		}
		
		$this->projects =& $projects['rows'];
		
		// Get recent e-mails
		//if ($this->iOfficeConfig->get('enable_email_client') && $this->settings->enable_email_client) {
			// Limit the number of entries to 5
			request::setVar('per_page', 5);
			require_once _ABS_PATH.DS.'components'.DS.'com_email'.DS.'models'.DS.'email.php';
			$modelEmail =& phpFrame::getInstance('emailModelEmail');
			$modelEmail->loadUserEmailAccount();
			$modelEmail->openStream('INBOX');
			$emails = $modelEmail->getMessageList();
			$modelEmail->closeStream();
			
			$this->emails =& $emails['res'];
		//}
		
		parent::display();
	}
}
?>