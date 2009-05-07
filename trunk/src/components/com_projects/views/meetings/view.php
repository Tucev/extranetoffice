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
 * projectsViewMeetings Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getViewName()).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class projectsViewMeetings extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('meetings', $layout);
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
		$this->_data['page_title'] = _LANG_MEETINGS;
		$this->page_heading = $this->project->name;
		
		parent::display();
		
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayMeetingsList() {
		phpFrame::getPathway()->addItem($this->page_title);
	}
	
	function displayMeetingsDetail() {
		$meetingid = phpFrame_Environment_Request::getVar('meetingid', 0);
		
		$modelMeetings = $this->getModel('meetings');
		$this->row = $modelMeetings->getMeetingsDetail($this->projectid, $meetingid);
		
		$document = phpFrame::getDocument('html');
		$document->addScript('lib/jquery/plugins/lightbox/jquery.lightbox-0.5.pack.js');
		$document->addStyleSheet('lib/jquery/plugins/lightbox/css/jquery.lightbox-0.5.css');
		
		$this->page_title .= ' - '.$meeting->name;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=meetings&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($meeting->name);
	}
	
	/**
	 * @todo Asignees ... sort them out
	 *
	 */
	function displayMeetingsForm() {
		
		$action = empty($this->data['row']) ? _LANG_MEETINGS_NEW : _LANG_MEETINGS_EDIT;
		$this->page_title .= ' - '.$action;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=meetings&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($action);
	}
	
	function displayMeetingsSlideshowsForm() {
		$this->meetingid = phpFrame_Environment_Request::getVar('meetingid', 0);
		$slideshowid = phpFrame_Environment_Request::getVar('slideshowid', 0);
		
		if (!empty($slideshowid)) {
			$action = _LANG_SLIDESHOWS_EDIT;
			$modelSlideshows = $this->getModel('meetings');
			$this->row = $modelSlideshows->getSlideshows($this->projectid, $this->meetingid, $slideshowid);
			$this->row = $this->row[0];
		}
		else {
			$action = _LANG_SLIDESHOWS_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=meetings&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($action);
		$this->action = $action;
	}
	
	function displayMeetingsFilesForm() {
		$this->meetingid = phpFrame_Environment_Request::getVar('meetingid', 0);
		
		if (!empty($this->meetingid)) {
			$modelFiles = $this->getModel('files');
			$this->project_files = $modelFiles->getFiles($this->projectid);
			$this->project_files = $this->project_files['rows'];
			
			$modelSlideshows = $this->getModel('meetings');
			$this->meeting_files = $modelSlideshows->getFiles($this->projectid, $this->meetingid);
			$this->meeting_files_ids = array();
			for ($i=0; $i<count($this->meeting_files); $i++) {
				$this->meeting_files_ids[] = $this->meeting_files[$i]->id;
			}
		}
		
		$action = _LANG_PROJECTS_MEETINGS_FILES_ATTACH;
		$this->page_title .= ' - '.$action;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=meetings&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($action);
		$this->action = $action;
	}
	
}
