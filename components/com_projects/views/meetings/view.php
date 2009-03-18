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
 * projectsViewMeetings Class
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
class projectsViewMeetings extends view {
	var $page_title=null;
	var $projectid=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		$this->meetingid =& request::getVar('meetingid', 0);
		
		// Set reference to project object loaded in controller
		if (!empty($this->projectid)) {
			$controller =& phpFrame::getInstance('projectsController');
			$this->project =& $controller->project;
		}
		
		$this->current_tool = _LANG_MEETINGS;
		
		parent::__construct();
	}
	
	/**
	 * Override view display method
	 * 
	 * This method overrides the parent display() method and appends the page title to the document title.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function display() {
		$this->page_title = _LANG_MEETINGS;
		$this->page_heading = $this->project->name;
		
		parent::display();
		
		// Append page title to document title
		$document =& factory::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayMeetingsList() {
		$modelMeetings =& $this->getModel('meetings');
		$meetings = $modelMeetings->getMeetings($this->projectid);
		$this->rows =& $meetings['rows'];
		$this->pageNav =& $meetings['pageNav'];
		$this->lists =& $meetings['lists'];
		
		$this->addPathwayItem($this->page_title);
	}
	
	function displayMeetingsDetail() {
		$modelMeetings =& $this->getModel('meetings');
		// Get meeting details
		$meeting = $modelMeetings->getMeetingsDetail($this->projectid, $this->meetingid);
		$this->row =& $meeting;
		
		$this->page_title .= ' - '.$meeting->name;
		$this->addPathwayItem($this->current_tool, "index.php?option=com_projects&view=meetings&projectid=".$this->projectid);
		$this->addPathwayItem($meeting->name);
	}
	
	/**
	 * @todo Asignees ... sort them out
	 *
	 */
	function displayMeetingsForm() {
		if (!empty($this->meetingid)) {
			$action = _LANG_MEETINGS_EDIT;
			$modelMeetings =& $this->getModel('meetings');
			$meeting = $modelMeetings->getMeetingsDetail($this->projectid, $this->meetingid);
			$this->row =& $meeting;
		}
		else {
			$action = _LANG_MEETINGS_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->addPathwayItem($this->current_tool, "index.php?option=com_projects&view=meetings&projectid=".$this->projectid);
		$this->addPathwayItem($action);
	}
	
	function displayMeetingsSlideshowsForm() {
		if (!empty($this->slideshowid)) {
			$action = _LANG_SLIDESHOWS_EDIT;
			$modelSlideshows =& $this->getModel('meetings');
			$slideshow = $modelSlideshows->getSlideshowsDetail($this->projectid, $this->slideshowid);
			$this->row =& $slideshow;
		}
		else {
			$action = _LANG_SLIDESHOWS_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->addPathwayItem($this->current_tool, "index.php?option=com_projects&view=meetings&projectid=".$this->projectid);
		$this->addPathwayItem($action);
	}
	
}
?>