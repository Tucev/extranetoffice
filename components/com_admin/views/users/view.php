<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
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
 * $tmpl_specific_method = "display".ucfirst(request::getVar('view')).ucfirst($this->tmpl);
 * </code>
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class adminViewUsers extends view {
	var $page_title=null;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		// Set the view template to load
		$this->layout =& request::getVar('layout', 'list');
		
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
		$document =& factory::getDocument('html');
		$document->title .= ' - '.$this->page_title;
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayUsersList() {
		$this->page_title = _LANG_ADMIN_USERS;
		
		// Push model into the view
		$model =& $this->getModel('users');
		$users = $model->getUsers();
		$this->rows =& $users['rows'];
		$this->pageNav =& $users['pageNav'];
		$this->lists =& $users['lists'];
	}
	
	function displayUsersForm() {
		$userid = request::getVar('userid', 0);
		
		$this->page_title = _LANG_ADMIN_USERS_NEW;
		
		// Push model into the view
		$model =& $this->getModel('users');
		$this->row = $model->getUsersDetail($userid);
	}
}
?>