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
 * projectsViewFiles Class
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
class projectsViewFiles extends view {
	var $page_title=null;
	var $projectid=null;
	
	function __construct() {
		// Set the view template to load (default value is set in controller)
		$this->layout =& request::getVar('layout');
		
		// Set reference to projectid
		$this->projectid =& request::getVar('projectid', 0);
		
		parent::__construct();
	}
	
	function displayFiles() {
		$this->addPathwayItem($this->page_subheading);
		
		$modelFiles = new iOfficeModelFiles();
		$files = $modelFiles->getFiles($this->projectid);
		$this->assignRef('rows', $files['rows']);
		$this->assignRef('pageNav', $files['pageNav']);
		$this->assignRef('lists', $files['lists']);
	}
	
	function displayFilesForm() {
		$this->page_title .= ' - '._LANG_FILES_NEW;
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));
		$this->addPathwayItem(_LANG_FILES_NEW);
		
		$parentid = request::getVar('parentid', 0);
		$parent_title = projectsHelperProjects::fileid2name($parentid);
		$this->assign('parentid', $parentid);
		$this->assign('parent_title', $parent_title);
	}
	
	function displayFilesDetail() {
		$this->addPathwayItem($this->page_subheading, route::_("index.php?option=com_projects&view=projects&layout=".$this->current_tool."&projectid=".$this->projectid));	
		
		$modelFiles = new iOfficeModelFiles();
		$file = $modelFiles->getFilesDetail($this->projectid, $this->fileid);
		$this->assignRef('row', $file);
		
		$this->page_title .= ' - '.$file->title;
		$this->addPathwayItem($file->title);
	}
	
}
?>