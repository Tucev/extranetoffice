<?php
/**
 * @version 	$Id$
 * @package		phpFrame
 * @subpackage	com_users
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * usersViewSettings Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		phpFrame
 * @subpackage 	com_users
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_View
 */
class usersViewSettings extends phpFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('settings', $layout);
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
		$this->_data['page_title'] = _LANG_USER_ACCOUNT;
		// Append page title to document title
		$document = phpFrame::getDocument('html');
		$document->title .= ' - '._LANG_USER_ACCOUNT;
		
		parent::display();
	}
}
?>