<?php
/**
 * @version 	$Id$
 * @package		PHPFrame
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

/**
 * adminViewAdmin Class
 * 
 * The methods in this class are invoked by its parent class. See display() 
 * method in 'view' class.
 * 
 * @package		PHPFrame
 * @subpackage 	com_admin
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		PHPFrame_Application_View
 */
class adminViewAdmin extends PHPFrame_Application_View {
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct($layout) {
		// Invoke the parent to set the view name and default layout
		parent::__construct('admin', $layout);
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
		// Set tmpl request var in view so it can be used to append to urls when
		// view is loaded with tmpl=component (that means no overall template).
		// This is useful for views being loaded via ajax
		$tmpl = PHPFrame::Request()->get('tmpl', '');
		if (!empty($tmpl)) {
			$tmpl = "&tmpl=".$tmpl;
		}
		$this->addData('tmpl', $tmpl);
		
		parent::display();
		
		// Append page title to document title
		$document = PHPFrame::getDocument('html');
		$document->title .= ' - '.$this->_data['page_title'];
	}
	
	/**
	 * Custom display method triggered by list layout.
	 * 
	 * @return void
	 */
	function displayAdmin() {
		$this->_data['page_title'] = _LANG_ADMIN;
	}
}
