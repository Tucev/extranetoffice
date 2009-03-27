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
 * projectsModelMeetings Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelMeetings extends model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	public function getMeetings($projectid) {
		$filter_order = request::getVar('filter_order', 'm.created');
		$filter_order_Dir = request::getVar('filter_order_Dir', 'DESC');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		//TODO: Have to apply access levels
		//$where[] = "( p.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";

		if ( $search ) {
			$where[] = "m.name LIKE '%".$this->db->getEscaped($search)."%'";
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
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();

		
		$pageNav = new pagination( $total, $limitstart, $limit );

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo $query; exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
				// get total comments
				$modelComments =& $this->getModel('comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'meetings');
			}
		}
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;
		
		// pack data into an array to return
		$return['rows'] = $rows;
		$return['pageNav'] = $pageNav;
		$return['lists'] = $lists;
		
		return $return;
	}
	
	public function getMeetingsDetail($projectid, $meetingid) {
		$query = "SELECT m.*, u.username AS created_by_name ";
		$query .= " FROM #__meetings AS m ";
		$query .= " JOIN #__users u ON u.id = m.created_by ";
		$query .= " WHERE m.id = ".$meetingid;
		$query .= " ORDER BY m.created DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($meetingid);
		
		// get slideshows
		$row->slideshows = $this->getSlideshows($projectid, $meetingid);
		
		// get files
		$row->files = $this->getFiles($projectid, $meetingid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
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
			$this->error[] = _LANG_SAVE_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		require_once COMPONENT_PATH.DS."tables".DS."meetings.table.php";		
		$row =& phpFrame::getInstance("projectsTableMeetings");
		
		if (empty($post['id'])) {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		else {
			$row->load($post['id']);
		}
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		// Delete existing assignees before we store new ones if editing existing issue
		if (!empty($post['id'])) {
			$query = "DELETE FROM #__users_meetings WHERE meetingid = ".$row->id;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_meetings ";
			$query .= " (id, userid, meetingid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
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
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Delete meetings assignees
		$query = "DELETE FROM #__users_meetings ";
		$query .= " WHERE meetingid = ".$meetingid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		// Delete meeting slideshows
		$query = "SELECT id FROM #__slideshows WHERE meetingid = ".$meetingid;
		$this->db->setQuery($query);
		$slideshows = $this->db->loadResultArray();
		if (is_array($slideshows) && count($slideshows) > 0) {
			foreach ($slideshows as $slideshowid) {
				if (!$this->deleteSlideshow($projectid, $slideshowid)) {
					return false;
				}
			}
		}
		
		// Instantiate table object
		require_once COMPONENT_PATH.DS."tables".DS."meetings.table.php";
		$row =& phpFrame::getInstance("projectsTableMeetings");
		
		// Delete row from database
		if (!$row->delete($meetingid)) {
			$this->error[] = $row->getLastError();
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
		$this->db->setQuery($query);
		$slideshows = $this->db->loadObjectList();
		
		// Get slideshows slides
		for ($i=0; $i<count($slideshows); $i++) {
			$query = "SELECT * ";
			$query .= " FROM #__slideshows_slides ";
			$query .= " WHERE slideshowid = ".$slideshows[$i]->id;
			$this->db->setQuery($query);
			$slideshows[$i]->slides = $this->db->loadObjectList();
		}
		
		return $slideshows;
	}
	
	function saveSlideshow($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->error[] = _LANG_SAVE_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		require_once COMPONENT_PATH.DS."tables".DS."slideshows.table.php";		
		$row =& phpFrame::getInstance("projectsTableSlideshows");
		
		if (!empty($post['id'])) {
			$row->load($post['id']);
		}
		else {
			$row->created_by = $this->user->id;
			$row->created = date("Y-m-d H:i:s");
		}
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
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
		$this->db->setQuery($query);
		$slides = $this->db->loadObjectList();
		
		if (is_array($slides) && count($slides) > 0) {
			foreach ($slides as $slide) {
				$file = _ABS_PATH.DS.$this->config->get('upload_dir').DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS.$slide->filename;
				if (file_exists($file) && !unlink($file)) {
					$this->error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
					return false;
				}
				$thumb = _ABS_PATH.DS.$this->config->get('upload_dir').DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS."thumb".DS.$slide->filename;
				if (file_exists($thumb) && !unlink($thumb)) {
					$this->error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
					return false;
				}
				$query = "DELETE FROM #__slideshows_slides WHERE id = ".$slide->id;
				$this->db->setQuery($query);
				if (!$this->db->query()) {
					$this->error[] = $this->db->getLastError();
					return false;
				}
			}
		}
		
		$slideshow_dir_thumb = _ABS_PATH.DS.$this->config->get('upload_dir').DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS."thumb".DS;
		if (is_dir($slideshow_dir_thumb) && !rmdir($slideshow_dir_thumb)) {
			$this->error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
			return false;
		}
		$slideshow_dir = _ABS_PATH.DS.$this->config->get('upload_dir').DS."projects".DS.$projectid.DS."slideshows".DS.$slideshowid.DS;
		if (is_dir($slideshow_dir) && !rmdir($slideshow_dir)) {
			$this->error[] = _LANG_MEETINGS_SLIDE_FILE_DELETE_ERROR;
			return false;
		}
		
		$query = "DELETE FROM #__slideshows ";
		$query .= " WHERE id = ".$slideshowid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
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
			$this->error[] = _LANG_SAVE_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		require_once COMPONENT_PATH.DS."tables".DS."slideshows_slides.table.php";		
		$row =& phpFrame::getInstance("projectsTableSlideshowsSlides");
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		// upload the file
		//TODO: Have to catch errors and look at file permissions
		$upload_dir = $this->config->get('upload_dir').DS."projects".DS.$post['projectid'].DS."slideshows".DS;
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
		$max_upload_size = $this->config->get('max_upload_size')*(1024*1024); // Mb
		$file = filesystem::uploadFile('filename', $upload_dir, $accept, $max_upload_size);
		
		if (!empty($file['error'])) {
			$this->error[] = $file['error'];
			return false;
		}
		
		// Resize image
		$image = new image();
		if (!$image->resize_image($upload_dir.$file['file_name'], $upload_dir.$file['file_name'], 764, 573)) {
			$this->error[] = _LANG_MEETINGS_SLIDE_RESIZE_ERROR;
			return false;
		}
		// Create thumbnail
		if (!$image->resize_image($upload_dir.$file['file_name'], $upload_dir."thumb".DS.$file['file_name'], 120, 90)) {
			$this->error[] = _LANG_MEETINGS_SLIDE_THUMBNAIL_ERROR;
			return false;
		}
		
		$row->filename = $file['file_name'];
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	public function deleteSlide($projectid, $slideid) {
		// Check whether a project id is included in the post array
		if (empty($projectid)) {
			$this->error[] = _LANG_SAVE_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		require_once COMPONENT_PATH.DS."tables".DS."slideshows_slides.table.php";		
		$row =& phpFrame::getInstance("projectsTableSlideshowsSlides");
		
		$row->load($slideid);
		
		$upload_dir = $this->config->get('upload_dir').DS."projects".DS.$projectid.DS."slideshows".DS.$row->slideshowid.DS;
		if (!unlink($upload_dir.$row->filename) || !unlink($upload_dir."thumb".DS.$row->filename)) {
			$this->error[] = _LANG_MEETINGS_SLIDE_DELETE_ERROR;
			return false;
		}
		else {
			$query = "DELETE FROM #__slideshows_slides WHERE id = ".$slideid;
			$this->db->setQuery($query);
			if ($this->db->query() === false) {
				$this->error[] = $this->db->getLastError();
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
		$this->db->setQuery($query);
		$fileids = $this->db->loadResultArray();
		
		// Get files data
		$files = array();
		for ($i=0; $i<count($fileids); $i++) {
			$query = "SELECT * ";
			$query .= " FROM #__files ";
			$query .= " WHERE id = ".$fileids[$i];
			$this->db->setQuery($query);
			$files[$i] = $this->db->loadObject();
		}
		
		return $files;
	}
	
	public function saveFiles($meetingid, $fileids) {
		if (empty($meetingid)) {
			$this->error[] = _LANG_PROJECTS_MEETINGS_NO_MEETING_SELECTED;
			return false;
		}
		
		if (!is_array($fileids)) {
			$fileids[] = $fileids;
		}
		
		$query = "DELETE FROM #__meetings_files WHERE meetingid = ".$meetingid;
		$this->db->setQuery($query);
		if (!$this->db->query()) {
			$this->error[] = $this->db->getLastError();
			return false;
		}
		
		foreach ($fileids as $fileid) {
			$query = "INSERT INTO #__meetings_files (`id`, `meetingid`, `fileid`) VALUES (NULL, ".$meetingid.", ".$fileid.")";
			$this->db->setQuery($query);
			if (!$this->db->query()) {
				$this->error[] = $this->db->getLastError();
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
		$this->db->setQuery($query);
		$assignees = $this->db->loadObjectList();
		
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			if ($asoc === false) {
				$new_assignees[$i] = $assignees[$i]->userid;
			}
			else {
				$new_assignees[$i]['id'] = $assignees[$i]->userid;
				$new_assignees[$i]['name'] = usersHelper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
				$new_assignees[$i]['email'] = $assignees[$i]->email;
			}
		}
		
		return $new_assignees;
	}
}
?>