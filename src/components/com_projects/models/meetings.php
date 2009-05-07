<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelMeetings Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class projectsModelMeetings extends phpFrame_Application_Model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {}
	
	public function getMeetings(phpFrame_Database_Listfilter $list_filter, $projectid) {
	
		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		//TODO: Have to apply access levels
		//$where[] = "( p.access = '0' OR (".$this->_user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";

		if ( $search ) {
			$where[] = "m.name LIKE '%".phpFrame::getDB()->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "m.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if ($filter_order == 'm.created'){
			$orderby = ' ORDER BY m.created DESC';
		} else {
			$orderby = ' ORDER BY m.created DESC, '. $filter_order .' '. $filter_order_Dir;
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  m.*, 
				  u.username AS created_by_name 
				  FROM #__meetings AS m 
				  JOIN #__users u ON u.id = m.created_by "
				  . $where . 
				  " GROUP BY m.id ";
		//echo $query; exit;	  
		phpFrame::getDB()->setQuery( $query );
		phpFrame::getDB()->query();
		
		$list_filter->setTotal(phpFrame::getDB()->getNumRows());

		// get the subset (based on limits) of required records
		$query .= $list_filter->getLimitStmt();
		
		//echo $query; exit;
		phpFrame::getDB()->setQuery($query);
		$rows = phpFrame::getDB()->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
				// get total comments
				$modelComments = $this->getModel('comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'meetings');
			}
		}
		
		return $return;
	}
	
	public function getMeetingsDetail($projectid, $meetingid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__meetings AS m ";
		$query .= " JOIN #__users u ON u.id = m.created_by ";
		$query .= " WHERE m.id = ".$meetingid;
		$query .= " ORDER BY m.created DESC";
		phpFrame::getDB()->setQuery($query);
		$row = phpFrame::getDB()->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($meetingid);
		
		// get slideshows
		$row->slideshows = $this->getSlideshows($projectid, $meetingid);
		
		// get files
		$row->files = $this->getFiles($projectid, $meetingid);
		
		// Get comments
		$modelComments = $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'meetings', $meetingid);
		
		return $row;
	}
	
	/**
	 * Save a project meeting
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveMeeting($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
				
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableMeetings");
		
		if (empty($post['id'])) {
			$row->created_by = $this->_user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		else {
			$row->load($post['id']);
		}
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if (!empty($post['id'])) {
			$query = "DELETE FROM #__users_meetings WHERE meetingid = ".$row->id;
			phpFrame::getDB()->setQuery($query);
			phpFrame::getDB()->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_meetings ";
			$query .= " (id, userid, meetingid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			phpFrame::getDB()->setQuery($query);
			phpFrame::getDB()->query();
		}
		
		return $row;
	}
	
	/**
	 * Delete a project meeting
	 * 
	 * This method also deletes the entries from users_meetings and any comments associated with the meeting.
	 * 
	 * @param	int		$projectid	The project id.
	 * @param	int		$meetingid	The id of the meeting we want to delete.
	 * @return	bool	Returns TRUE on success or FALSE on error.
	 */
	public function deleteMeeting($projectid, $meetingid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Delete meetings comments
		$query = "DELETE FROM #__comments ";
		$query .= " WHERE projectid = ".$projectid." AND type = 'meetings' AND itemid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		if (!phpFrame::getDB()->query()) {
			$this->_error[] = phpFrame::getDB()->getLastError();
			return false;
		}
		
		// Delete meetings assignees
		$query = "DELETE FROM #__users_meetings ";
		$query .= " WHERE meetingid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		if (!phpFrame::getDB()->query()) {
			$this->_error[] = phpFrame::getDB()->getLastError();
			return false;
		}
		
		// Delete meeting slideshows
		$query = "SELECT id FROM #__slideshows WHERE meetingid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		$slideshows = phpFrame::getDB()->loadResultArray();
		if (is_array($slideshows) && count($slideshows) > 0) {
			foreach ($slideshows as $slideshowid) {
				if (!$this->deleteSlideshow($projectid, $slideshowid)) {
					return false;
				}
			}
		}
		
		// Instantiate table object
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableMeetings");
		
		// Delete row from database
		if (!$row->delete($meetingid)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	public function getSlideshows($projectid, $meetingid, $slideshowid=0) {
		$query = "SELECT * ";
		$query .= " FROM #__slideshows ";
		$query .= " WHERE projectid = ".$projectid." AND meetingid = ".$meetingid;
		if (!empty($slideshowid)) $query .= " AND id = ".$slideshowid; 
		phpFrame::getDB()->setQuery($query);
		$slideshows = phpFrame::getDB()->loadObjectList();
		
		// Get slideshows slides
		for ($i=0; $i<count($slideshows); $i++) {
			$query = "SELECT * ";
			$query .= " FROM #__slideshows_slides ";
			$query .= " WHERE slideshowid = ".$slideshows[$i]->id;
			phpFrame::getDB()->setQuery($query);
			$slideshows[$i]->slides = phpFrame::getDB()->loadObjectList();
		}
		
		return $slideshows;
	}
	
	function saveSlideshow($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
			
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableSlideshows");
		
		if (!empty($post['id'])) {
			$row->load($post['id']);
		}
		else {
			$row->created_by = $this->_user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	function deleteSlideshow($projectid, $slideshowid) {
		if (empty($projectid) || empty($slideshowid)) {
			return false;
		}
		
		// Get slidesw to delete them first
		$query = "SELECT * ";
		$query .= " FROM #__slideshows_slides ";
		$query .= " WHERE slideshowid = ".$slideshowid;
		phpFrame::getDB()->setQuery($query);
		$slides = phpFrame::getDB()->loadObjectList();
		
		if (is_array($slides) && count($slides) > 0) {
			foreach ($slides as $slide) {
				$file = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS.$slide->filename;
				if (file_exists($file) && !unlink($file)) {
					$this->_error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
					return false;
				}
				$thumb = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS."thumb".DS.$slide->filename;
				if (file_exists($thumb) && !unlink($thumb)) {
					$this->_error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
					return false;
				}
				$query = "DELETE FROM #__slideshows_slides WHERE id = ".$slide->id;
				phpFrame::getDB()->setQuery($query);
				if (!phpFrame::getDB()->query()) {
					$this->_error[] = phpFrame::getDB()->getLastError();
					return false;
				}
			}
		}
		
		$slideshow_dir_thumb = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS."thumb".DS;
		if (is_dir($slideshow_dir_thumb) && !rmdir($slideshow_dir_thumb)) {
			$this->_error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
			return false;
		}
		$slideshow_dir = _ABS_PATH.DS.config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS;
		if (is_dir($slideshow_dir) && !rmdir($slideshow_dir)) {
			$this->_error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
			return false;
		}
		
		$query = "DELETE FROM #__slideshows ";
		$query .= " WHERE id = ".$slideshowid;
		phpFrame::getDB()->setQuery($query);
		if (!phpFrame::getDB()->query()) {
			$this->_error[] = phpFrame::getDB()->getLastError();
			return false;
		}
		
		return true;
	}
	
	/**
	 * Save a slide in a meeting slideshow
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function uploadSlide($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
			
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableSlideshowsSlides");
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		// upload the file
		//TODO: Have to catch errors and look at file permissions
		$upload_dir = config::UPLOAD_DIR.DS."projects".DS.$post['projectid'].DS."slideshows".DS;
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0711);
		}
		$upload_dir .= $post['slideshowid'].DS;
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0711);
		}
		if (!is_dir($upload_dir."thumb".DS)) {
			mkdir($upload_dir."thumb".DS, 0711);
		}
		$accept = 'image/jpg,image/jpeg,image/png,image/gif'; // mime types
		$max_upload_size = config::MAX_UPLOAD_SIZE*(1024*1024); // Mb
		$file = phpFrame_Utils_Filesystem::uploadFile('filename', $upload_dir, $accept, $max_upload_size);
		
		if (!empty($file['error'])) {
			$this->_error[] = $file['error'];
			return false;
		}
		
		// Resize image
		$image = new phpFrame_Utils_Image();
		if (!$image->resize_image($upload_dir.$file['file_name'], $upload_dir.$file['file_name'], 764, 573)) {
			$this->_error[] = _LANG_MEETINGS_SLIDE_RESIZE_ERROR;
			return false;
		}
		// Create thumbnail
		if (!$image->resize_image($upload_dir.$file['file_name'], $upload_dir."thumb".DS.$file['file_name'], 120, 90)) {
			$this->_error[] = _LANG_MEETINGS_SLIDE_THUMBNAIL_ERROR;
			return false;
		}
		
		$row->filename = $file['file_name'];
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	public function deleteSlide($projectid, $slideid) {
		// Check whether a project id is included in the post array
		if (empty($projectid)) {
			$this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
				
		$row =& phpFrame_Base_Singleton::getInstance("projectsTableSlideshowsSlides");
		
		$row->load($slideid);
		
		$upload_dir = config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$row->slideshowid.DS;
		if (!unlink($upload_dir.$row->filename) || !unlink($upload_dir."thumb".DS.$row->filename)) {
			$this->_error[] = _LANG_MEETINGS_SLIDE_DELETE_ERROR;
			return false;
		}
		else {
			$query = "DELETE FROM #__slideshows_slides WHERE id = ".$slideid;
			phpFrame::getDB()->setQuery($query);
			if (phpFrame::getDB()->query() === false) {
				$this->_error[] = phpFrame::getDB()->getLastError();
				return false;
			}
			else {
				return true;
			}	
		}
	}
	
	public function getFiles($projectid, $meetingid) {
		$query = "SELECT fileid ";
		$query .= " FROM #__meetings_files ";
		$query .= " WHERE meetingid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		$fileids = phpFrame::getDB()->loadResultArray();
		
		// Get files data
		$files = array();
		for ($i=0; $i<count($fileids); $i++) {
			$query = "SELECT * ";
			$query .= " FROM #__files ";
			$query .= " WHERE id = ".$fileids[$i];
			phpFrame::getDB()->setQuery($query);
			$files[$i] = phpFrame::getDB()->loadObject();
		}
		
		return $files;
	}
	
	public function saveFiles($meetingid, $fileids) {
		if (empty($meetingid)) {
			$this->_error[] = _LANG_PROJECTS_MEETINGS_NO_MEETING_SELECTED;
			return false;
		}
		
		if (!is_array($fileids)) {
			$fileids[] = $fileids;
		}
		
		$query = "DELETE FROM #__meetings_files WHERE meetingid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		if (!phpFrame::getDB()->query()) {
			$this->_error[] = phpFrame::getDB()->getLastError();
			return false;
		}
		
		foreach ($fileids as $fileid) {
			$query = "INSERT INTO #__meetings_files (`id`, `meetingid`, `fileid`) VALUES (NULL, ".$meetingid.", ".$fileid.")";
			phpFrame::getDB()->setQuery($query);
			if (!phpFrame::getDB()->query()) {
				$this->_error[] = phpFrame::getDB()->getLastError();
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Get list of assignees
	 *
	 * @param	int		$meetingid
	 * @param	bool	$asoc
	 * @return	array	Array containing assignees ids or asociative array with id, name and email if asoc is true.
	 */
	public function getAssignees($meetingid, $asoc=true) {
		$query = "SELECT um.userid, u.firstname, u.lastname, u.email";
		$query .= " FROM #__users_meetings AS um ";
		$query .= "LEFT JOIN #__users u ON u.id = um.userid";
		$query .= " WHERE um.meetingid = ".$meetingid;
		phpFrame::getDB()->setQuery($query);
		$assignees = phpFrame::getDB()->loadObjectList();
		
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			if ($asoc === false) {
				$new_assignees[$i] = $assignees[$i]->userid;
			}
			else {
				$new_assignees[$i]['id'] = $assignees[$i]->userid;
				$new_assignees[$i]['name'] = phpFrame_User_Helper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
				$new_assignees[$i]['email'] = $assignees[$i]->email;
			}
		}
		
		return $new_assignees;
	}
}
