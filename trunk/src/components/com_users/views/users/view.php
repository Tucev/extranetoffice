<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * usersViewUsers Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * Method name to be triggered will be formed as follows:
 * 
 * <code>
 * $tmpl_specific_method = "display".ucfirst(phpFrame_Environment_Request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class usersViewUsers extends phpFrame_Application_View {
	var $page_title=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& phpFrame_Environment_Request::getVar('layout');
		
		parent::__construct();
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
		if (phpFrame_Environment_Request::getVar('layout') != 'list') {
			$document =& phpFrame::getDocument('html');
			$document->title .= ' - '.$this->page_title;
		}
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayUsersList() {
		$this->page_title = _LANG_USERS;
		
		$modelUsers = $this->getModel('users');
		$users = $modelUsers->getUsers();
		$this->rows =& $users['rows'];
		$this->pageNav =& $users['pageNav'];
		$this->lists =& $users['lists'];
	}
	
	/**
	 * Custom display method triggered by detail layout.
	 * 
	 * @return void
	 */
	function displayUsersDetail() {
		$userid = phpFrame_Environment_Request::getVar('userid', 0);
		
		$modelUsers = $this->getModel('users');
		$this->row = $modelUsers->getUsersDetail($userid);
		
		$this->page_title = $this->row->firstname.' '.$this->row->lastname;
		$this->addPathwayItem($this->row->firstname.' '.$this->row->lastname);
	}
}
?>