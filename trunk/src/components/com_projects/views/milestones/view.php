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
 * projectsViewMilestones Class
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
class projectsViewMilestones extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('milestones', $layout);
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
		$this->_data['page_title'] = _LANG_MILESTONES;
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
	function displayMilestonesList() {
		$modelMilestones = $this->getModel('milestones');
		$milestones = $modelMilestones->getMilestones($this->projectid);
		$this->rows =& $milestones['rows'];
		$this->pageNav =& $milestones['pageNav'];
		$this->lists =& $milestones['lists'];
		
		phpFrame::getPathway()->addItem($this->page_title);
	}
	
	function displayMilestonesDetail() {
		$modelMilestones = $this->getModel('milestones');
		$this->row = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
		
		$this->page_title .= ' - '.$this->row->title;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=milestones&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($this->row->title);
	}
	
	function displayMilestonesForm() {
		if (!empty($this->milestoneid)) {
			$action = _LANG_MILESTONES_EDIT;
			$modelMilestones = $this->getModel('milestones');
			$this->row = $modelMilestones->getMilestonesDetail($this->projectid, $this->milestoneid);
		}
		else {
			$action = _LANG_MILESTONES_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		phpFrame::getPathway()->addItem($this->current_tool, "index.php?component=com_projects&view=milestones&projectid=".$this->projectid);
		phpFrame::getPathway()->addItem($action);
	}
}
