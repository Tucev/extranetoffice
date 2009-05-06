<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * adminViewUsers Class
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
 * @package		phpFrame
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class adminViewUsers extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	public function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('users', $layout);
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
		
		parent::display();
		
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '.$this->_data['page_title'];
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayUsersList() {
		$this->_data['page_title'] = _LANG_ADMIN_USERS;
	}
	
	function displayUsersForm() {
		if (empty($this->_data['row']->id)) {
			$this->_data['page_title'] = _LANG_ADMIN_USERS_NEW;
		}
		else {
			$this->_data['page_title'] = _LANG_ADMIN_USERS_EDIT;
		}
	}
}
