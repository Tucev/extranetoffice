<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsViewMeetings Class
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
class projectsViewMeetings extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
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
     * @return    void
     * @since    1.0
     */
    function display() {
        $this->_data['page_title'] = _LANG_MEETINGS;
        $this->_data['page_heading'] = $this->_data['project']->name;
        
        parent::display();
        
        // Append page title to document title
        $document = PHPFrame::Response()->getDocument();
        $document->title .= ' - '.$this->_data['page_title'];
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @return void
     */
    protected function displayMeetingsList() {
        $this->getPathway()->addItem(_LANG_MEETINGS);
    }
    
    protected function displayMeetingsDetail() {
        // Add jQuery lightbox plugin
        $document = PHPFrame::Response()->getDocument();
        $document->addScript('lib/jquery/plugins/lightbox/jquery.lightbox-0.5.pack.js');
        $document->addStyleSheet('lib/jquery/plugins/lightbox/css/jquery.lightbox-0.5.css');
        
        $this->_data['page_title'] .= ' - '.$this->_data['row']->name;
        $this->getPathway()->addItem(_LANG_MEETINGS, "index.php?component=com_projects&action=get_meetings&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($this->_data['row']->name);
    }
    
    /**
     * @todo Asignees ... sort them out
     *
     */
    protected function displayMeetingsForm() {
        $action = empty($this->_data['row']->id) ? _LANG_MEETINGS_NEW : _LANG_MEETINGS_EDIT;
        $this->_data['page_title'] .= ' - '.$action;
        $this->getPathway()->addItem(_LANG_MEETINGS, "index.php?component=com_projects&action=get_meetings&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($action);
        
        $this->addData('action', $action);
    }
    
    protected function displayMeetingsSlideshowsForm() {
        $action = empty($this->_data['row']->id) ? _LANG_SLIDESHOWS_NEW : _LANG_SLIDESHOWS_EDIT;
        $this->_data['page_title'] .= ' - '.$action;
        $this->getPathway()->addItem(_LANG_MEETINGS, "index.php?component=com_projects&action=get_meetings&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($action);
        
        $this->addData('action', $action);
    }
    
    protected function displayMeetingsFilesForm() {
        $action = _LANG_PROJECTS_MEETINGS_FILES_ATTACH;
        $this->_data['page_title'] .= ' - '.$action;
        $this->getPathway()->addItem(_LANG_MEETINGS, "index.php?component=com_projects&action=get_meetings&projectid=".$this->_data['project']->id);
        $this->getPathway()->addItem($action);
        
        $this->addData('action', $action);
    }
    
}
