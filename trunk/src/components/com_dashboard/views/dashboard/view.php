<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage    com_dashboard
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * dashboardViewDashboard Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package        PHPFrame
 * @subpackage     com_dashboard
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_View
 */
class dashboardViewDashboard extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
     */
    function __construct($layout) {
        // Invoke the parent to set the view name and default layout
        parent::__construct('dashboard', $layout);
    }
    
    /**
     * Override view display method
     * 
     * This method overrides the parent display() method and appends the page title to the document title.
     * 
     * @todo    This method needs to be re-written to treat dashboard items as modules.
     * @return    void
     * @since    1.0
     */
    function display() {
        $this->_data['page_title'] = _LANG_DASHBOARD;
        
        PHPFrame::Response()->setTitle(_LANG_DASHBOARD);
        
        parent::display();
    }
}
?>