<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsViewFiles Class
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
class projectsViewFiles extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
     */
    function __construct($layout) {
        // Invoke the parent to set the view name and default layout
        parent::__construct('files', $layout);
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
        $this->addData('page_title', _LANG_FILES);
        $this->addData('page_heading', $this->_data['project']->name);
        
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
    function displayFilesList() {
        $this->getPathway()->addItem($this->_data['page_title']);
    }
    
    function displayFilesForm() {
        $this->_data['page_title'] .= ' - '._LANG_FILES_NEW;
        $this->getPathway()->addItem($this->current_tool, PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&view=files&projectid=".$this->projectid));
        $this->getPathway()->addItem(_LANG_FILES_NEW);
    }
    
    function displayFilesDetail() {
        $this->_data['page_title'] .= ' - '.$this->row->title;
        $this->getPathway()->addItem($this->current_tool, PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&view=files&projectid=".$this->projectid));
        $this->getPathway()->addItem($this->row->title);
    }
    
}
