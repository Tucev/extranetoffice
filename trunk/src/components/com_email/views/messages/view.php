<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_email
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * emailViewMessages Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package        ExtranetOffice
 * @subpackage     com_email
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_View
 */
class emailViewMessages extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
     */
    function __construct($layout) {
        // Invoke the parent to set the view name and default layout
        parent::__construct('messages', $layout);
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
            $document = PHPFrame::Response()->getDocument();
            $document->title .= ' - '.$this->_data['page_title'];
        }
    }
    
    /**
     * Display messages list layout
     * 
     * Custom display method triggered by list layout.
     * 
     * @return void
     */
    function displayMessagesList() {
        $this->_data['page_title'] = _LANG_EMAIL;
        
        // Attach scripts and stylesheets
        $document = PHPFrame::Response()->getDocument();
        $document->addScript('lib/contextmenu/webtoolkit.contextmenu.js');
        $document->addStyleSheet('lib/contextmenu/webtoolkit.contextmenu.css');
            
        // Set the page to auto refresh every set amount of time (in seconds)
        //$document = PHPFrame::Response()->getDocument();
        //$document->setMetaData('refresh', '120', true);
    }
    
    /**
     * Display message detail layout
     * 
     * This method is a custom display method triggered by detail layout.
     * 
     * @return void
     */
    function displayMessagesDetail($uid=0) {
        $this->_data['page_title'] = _LANG_EMAIL_MESSAGE_DETAIL;
        $this->getPathway()->addItem($this->page_title);
        
        if (empty($uid)) {
            $uid = PHPFrame::Request()->get('uid', 0);
        }
        
        if (empty($uid)) {
            PHPFrame_Application_Error::raise('', 'error', 'No message was selected');
            return false;
        }
        else {
            // Get message details
            $model = $this->getModel('email');
            $model->loadUserEmailAccount();
        
            $model->openStream($this->folder);
            $message = $model->getMessageDetail($uid);
            $model->closeStream();
            $this->message =& $message;
        }
        
    }
    
    /**
     * Display project detail layout
     * 
     * This method is a custom display method triggered by detail layout.
     * 
     * @return void
     */
    function displayMessagesForm() {
        $this->_data['page_title'] = _LANG_EMAIL_NEW;
        
        $model = $this->getModel('email');
        $model->loadUserEmailAccount();
        $this->account =& $model->account;
        
        $this->getPathway()->addItem($this->page_title);
    }
}
?>