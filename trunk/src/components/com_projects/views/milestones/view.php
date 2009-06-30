<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsViewMilestones Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_View
 */
class projectsViewMilestones extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
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
     * @return    void
     * @since    1.0
     */
    function display() {
        $this->_data['page_title'] = _LANG_MILESTONES;
        $this->_data['page_heading'] = $this->_data['project']->name;
        
        parent::display();
        
        // Append page title to document title
        $document = PHPFrame::Response()->getDocument();
        $document->title .= ' - '._LANG_MILESTONES;
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @return void
     */
    protected function displayMilestonesList() {
        $this->getPathway()->addItem(_LANG_MILESTONES);
    }
    
    protected function displayMilestonesDetail() {
        $this->_data['page_title'] .= ' - '.$this->_data['row']->title;
        $this->getPathway()->addItem(_LANG_MILESTONES, "index.php?component=com_projects&action=get_milestones&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($this->_data['row']->title);
    }
    
    protected function displayMilestonesForm() {
        $action = empty($this->_data['row']->id) ? _LANG_MILESTONES_NEW : _LANG_MILESTONES_EDIT;
        $this->_data['page_title'] .= ' - '.$action;
        $this->getPathway()->addItem(_LANG_MILESTONES, "index.php?component=com_projects&action=get_milestones&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($action);
        
        $this->addData('action', $action);
    }
}
