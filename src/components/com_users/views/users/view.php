<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage    com_users
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * usersViewUsers Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package        PHPFrame
 * @subpackage     com_users
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_View
 */
class usersViewUsers extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
     */
    function __construct($layout) {
        // Invoke the parent to set the view name and default layout
        parent::__construct('users', $layout);
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
        parent::display();
        
        // Append page title to document title
        if ($this->_layout != 'list') {
            $document = PHPFrame::getDocument('html');
            $document->title .= ' - '.$this->page_title;
        }
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @return void
     */
    function displayUsersList() {
        $this->_data['page_title'] = _LANG_USERS;
    }
    
    /**
     * Custom display method triggered by detail layout.
     * 
     * @return void
     */
    function displayUsersDetail() {
        $this->_data['page_title'] = $this->_data['row']->firstname.' '.$this->_data['row']->lastname;
        $this->getPathway()->addItem($this->_data['row']->firstname.' '.$this->_data['row']->lastname);
    }
}
