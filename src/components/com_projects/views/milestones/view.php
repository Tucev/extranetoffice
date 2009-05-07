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
		$this->_data['page_heading'] = $this->_data['project']->name;
		
		parent::display();
		
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '._LANG_MILESTONES;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayMilestonesList() {
		phpFrame::getPathway()->addItem(_LANG_MILESTONES);
	}
	
	function displayMilestonesDetail() {
		$this->_data['page_title'] .= ' - '.$this->_data['row']->title;
		phpFrame::getPathway()->addItem(_LANG_MILESTONES, "index.php?component=com_projects&action=get_milestones&projectid=".$this->_data['project']->id);
		phpFrame::getPathway()->addItem($this->_data['row']->title);
	}
	
	function displayMilestonesForm() {
		$action = empty($this->_data['row']->id) ? _LANG_MILESTONES_NEW : _LANG_MILESTONES_EDIT;
		$this->_data['page_title'] .= ' - '.$action;
		phpFrame::getPathway()->addItem(_LANG_MILESTONES, "index.php?component=com_projects&action=get_milestones&projectid=".$this->_data['project']->id);
		phpFrame::getPathway()->addItem($action);
		
		$this->addData('action', $action);
	}
}
