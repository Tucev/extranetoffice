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
 * dashboardViewDashboard Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getView()).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		phpFrame
 * @subpackage 	com_dashboard
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class dashboardViewDashboard extends phpFrame_Application_View {
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
		$modelProjects = phpFrame_Base_Singleton::getInstance('projectsModelProjects');
		$projects = $modelProjects->getProjects($this->_user->id);
		// Get project updates, overdue items and upcoming milestones
		if (is_array($projects['rows']) && count($projects['rows']) > 0) {
			foreach ($projects['rows'] as $row) {
				// Get project updates
				$modelActivitylog =& phpFrame_Base_Singleton::getInstance('projectsModelActivitylog');
				$row->activitylog = $modelActivitylog->getActivityLog($row->id);
				
				// Get overdue issues
				$modelIssues =& phpFrame_Base_Singleton::getInstance('projectsModelIssues');
				$row->overdue_issues = $modelIssues->getTotalIssues($row->id, true);
			}
		}
		
		$this->projects =& $projects['rows'];
		
		// Get recent e-mails
		$this->emails = array();
		//if ($this->iOfficeConfig->get('enable_email_client') && $this->settings->enable_email_client) {
			// Limit the number of entries to 5
			/* Temporarily commented out.
			phpFrame_Environment_Request::setVar('per_page', 5);
			require_once _ABS_PATH.DS.'components'.DS.'com_email'.DS.'models'.DS.'email.php';
			$modelEmail =& phpFrame_Base_Singleton::getInstance('emailModelEmail');
			$modelEmail->loadUserEmailAccount();
			$modelEmail->openStream('INBOX');
			$emails = $modelEmail->getMessageList();
			$modelEmail->closeStream();
			
			$this->emails =& $emails['res'];
			*/
		//}
		
		parent::display();
	}
}
?>