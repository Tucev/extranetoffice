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
 * projectsViewMeetings Class
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
class projectsViewMeetings extends view {
	var $page_title=null;
	var $projectid=null;
	
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	function displayMeetingsList() {
		$this->addPathwayItem($this->page_subheading);
		
		$document =& factory::getDocument('html');
		$document->addScript('lib/jquery/jquery-1.3.1.min.js');
		$document->addScript('lib/thickbox/thickbox-compressed.js');
		$document->addStyleSheet('lib/thickbox/thickbox.css');
		
		/*$modelMeetings = new iOfficeModelMeetings();
		$meetings = $modelMeetings->getMeetings($this->projectid);
		$this->assignRef('rows', $meetings['rows']);
		$this->assignRef('pageNav', $meetings['pageNav']);
		$this->assignRef('lists', $meetings['lists']);*/
	}
	
	function displayMeetingsDetail() {
		$document =& JFactory::getDocument();
		$document->addStyleSheet('administrator/components/com_projects/lib/slimbox/css/slimbox.css');
		$document->addScript('media/system/js/mootools.js');
		$document->addScript('administrator/components/com_projects/lib/slimbox/js/slimbox.js');
		
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));	
		
		$modelMeetings = new iOfficeModelMeetings();
		// Get meeting details
		$meeting = $modelMeetings->getMeetingsDetail($this->projectid, $this->meetingid);
		$this->assignRef('row', $meeting);
		
		$this->page_title .= ' - '.$meeting->name;
		$this->addPathwayItem($meeting->name);
	}
	
	function displayMeetingsForm() {
		if (!empty($this->meetingid)) {
			$action = _LANG_MEETINGS_EDIT;
			$modelMeetings = new iOfficeModelMeetings();
			$meeting = $modelMeetings->getMeetingsDetail($this->projectid, $this->meetingid);
			$this->assignRef('row', $meeting);
		}
		else {
			$action = _LANG_MEETINGS_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem($action);
	}
	
	function displayMeetingsSlideshowsForm() {
		if (!empty($this->slideshowid)) {
			$action = _LANG_SLIDESHOWS_EDIT;
			$modelSlideshows = new iOfficeModelMeetings();
			$slideshow = $modelSlideshows->getSlideshowsDetail($this->projectid, $this->slideshowid);
			$this->assignRef('row', $slideshow);
		}
		else {
			$action = _LANG_SLIDESHOWS_NEW;
			// default values	
		}
		
		$this->page_title .= ' - '.$action;
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem($action);
	}
	
}
?>