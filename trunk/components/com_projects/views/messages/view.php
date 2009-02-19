<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsViewMessages Class
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
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		view, controller
 */
class projectsViewMessages extends view {
	var $page_title=null;
	var $projectid=null;
	
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	function displayMessages() {
		$this->addPathwayItem($this->page_subheading);
		
		$modelMessages = new iOfficeModelMessages();
		$messages = $modelMessages->getMessages($this->projectid);
		$this->assignRef('rows', $messages['rows']);
		$this->assignRef('pageNav', $messages['pageNav']);
		$this->assignRef('lists', $messages['lists']);
	}
	
	function displayMessagesDetail() {
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		
		$modelMessages = new iOfficeModelMessages();
		$message = $modelMessages->getMessagesDetail($this->projectid, $this->messageid);
		$this->assignRef('row', $message);
		
		$this->page_title .= ' - '.$message->subject;
		$this->addPathwayItem($message->subject);
	}
	
	function displayMessagesForm() {
		$this->page_title .= ' - '._LANG_MESSAGES_NEW;
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem(_LANG_MESSAGES_NEW);
	}
}
?>