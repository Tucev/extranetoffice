<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsModelMeetings Class
 * 
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_Model
 */
class projectsModelMeetings extends PHPFrame_MVC_Model {
    /**
     * Constructor
     *
     * @since 1.0.1
     */
    function __construct() {}
    
    /**
     * Get meetings
     * 
     * @param    object    $list_filter    Object of type PHPFrame_Database_CollectionFilter
     * @param    int        $projectid
     * @return    array
     */
    public function getMeetings(PHPFrame_Database_CollectionFilter $list_filter, $projectid) {
        $where = array();
        
        // Show only public projects or projects where user has an assigned role
        //TODO: Have to apply access levels
        //$where[] = "( p.access = '0' OR (".PHPFrame::Session()->getUser()->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
        
        $search = $list_filter->getSearchStr();
        if ( $search ) {
            $where[] = "m.name LIKE '%".PHPFrame::DB()->getEscaped($search)."%'";
        }
        
        if (!empty($projectid)) {
            $where[] = "m.projectid = ".$projectid;    
        }

        $where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

        // get the total number of records
        // This query groups the files by parentid so and retireves the latest revision for each file in current project
        $query = "SELECT 
                  m.*, 
                  u.username AS created_by_name 
                  FROM #__meetings AS m 
                  JOIN #__users u ON u.id = m.created_by "
                  . $where . 
                  " GROUP BY m.id ";
        //echo str_replace('#__', 'eo_', $query); exit;
        
        // Run query to get total rows before applying filter
        $list_filter->setTotal(PHPFrame::DB()->query($query)->rowCount());

        // Add order by and limit statements for subset (based on filter)
        //$query .= $list_filter->getOrderBySQL();
        $query .= $list_filter->getLimitSQL();
        //echo str_replace('#__', 'eo_', $query); exit;
        
        //echo $query; exit;
        $rows = PHPFrame::DB()->loadObjectList($query);
        
        // Prepare rows and add relevant data
        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                // Get assignees
                $row->assignees = $this->getAssignees($row->id);
                
                // get total comments
                $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
                $row->comments = $modelComments->getTotalComments($row->id, 'meetings');
            }
        }
        
        return $rows;
    }
    
    public function getMeetingsDetail($projectid, $meetingid) {
        $query = "SELECT m.*, u.username AS created_by_name ";
        $query .= " FROM #__meetings AS m ";
        $query .= " JOIN #__users u ON u.id = m.created_by ";
        $query .= " WHERE m.id = ".$meetingid;
        $query .= " ORDER BY m.created DESC";
        $row = PHPFrame::DB()->loadObject($query);
        
        // Get assignees
        $row->assignees = $this->getAssignees($meetingid);
        
        // get slideshows
        $row->slideshows = $this->getSlideshows($projectid, $meetingid);
        
        // get files
        $row->files = $this->getFiles($projectid, $meetingid);
        
        // Get comments
        $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
        $row->comments = $modelComments->getComments($projectid, 'meetings', $meetingid);
        
        return $row;
    }
    
    /**
     * Save a project meeting
     * 
     * @param    $post    The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
     * @return    mixed    Returns the stored table row object on success or FALSE on failure
     */
    public function saveMeeting($post) {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = $this->getTable('meetings');
        
        if (empty($post['id'])) {
            $row->created_by = PHPFrame::Session()->getUser()->id;
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
            PHPFrame::DB()->query($query);
        }
        
        // Store assignees
        if (is_array($post['assignees']) && count($post['assignees']) > 0) {
            $query = "INSERT INTO #__users_meetings ";
            $query .= " (id, userid, meetingid) VALUES ";
            for ($i=0; $i<count($post['assignees']); $i++) {
                if ($i>0) { $query .= ","; }
                $query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
            }
            
            PHPFrame::DB()->query($query);
        }
        
        return $row;
    }
    
    /**
     * Delete a project meeting
     * 
     * This method also deletes the entries from users_meetings and any comments associated with the meeting.
     * 
     * @param    int        $projectid    The project id.
     * @param    int        $meetingid    The id of the meeting we want to delete.
     * @return    bool    Returns TRUE on success or FALSE on error.
     */
    public function deleteMeeting($projectid, $meetingid) {
        //TODO: This function should allow ids as either int or array of ints.
        //TODO: This function should also check permissions before deleting
        
        // Delete meetings comments
        $query = "DELETE FROM #__comments ";
        $query .= " WHERE projectid = ".$projectid." AND type = 'meetings' AND itemid = ".$meetingid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        // Delete meetings assignees
        $query = "DELETE FROM #__users_meetings ";
        $query .= " WHERE meetingid = ".$meetingid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        // Delete meeting slideshows
        $query = "SELECT id FROM #__slideshows WHERE meetingid = ".$meetingid;
        $slideshows = PHPFrame::DB()->loadResultArray($query);
        if (is_array($slideshows) && count($slideshows) > 0) {
            foreach ($slideshows as $slideshowid) {
                if (!$this->deleteSlideshow($projectid, $slideshowid)) {
                    return false;
                }
            }
        }
        
        // Instantiate table object
        $row =& PHPFrame_Base_Singleton::getInstance("projectsTableMeetings");
        
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
        $slideshows = PHPFrame::DB()->loadObjectList($query);
        
        // Get slideshows slides
        for ($i=0; $i<count($slideshows); $i++) {
            $query = "SELECT * ";
            $query .= " FROM #__slideshows_slides ";
            $query .= " WHERE slideshowid = ".$slideshows[$i]->id;
            $slideshows[$i]->slides = PHPFrame::DB()->loadObjectList($query);
        }
        
        return $slideshows;
    }
    
    function saveSlideshow($post) {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
    
        $row = $this->getTable('slideshows');
        
        if (!empty($post['id'])) {
            $row->load($post['id']);
        }
        else {
            $row->created_by = PHPFrame::Session()->getUser()->id;
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
        $slides = PHPFrame::DB()->loadObjectList($query);
        
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
                if (!PHPFrame::DB()->query($query)) {
                    $this->_error[] = PHPFrame::DB()->getLastError();
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
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        return true;
    }
    
    /**
     * Save a slide in a meeting slideshow
     * 
     * @param    $post    The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
     * @return    mixed    Returns the stored table row object on success or FALSE on failure
     */
    public function uploadSlide($post) {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = $this->getTable('slideshowsSlides');
        
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
        $file = PHPFrame_Utils_Filesystem::uploadFile('filename', $upload_dir, $accept, $max_upload_size);
        
        if (!empty($file['error'])) {
            $this->_error[] = $file['error'];
            return false;
        }
        
        // Resize image
        $image = new PHPFrame_Utils_Image();
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
        
        $row = $this->getTable('slideshowsSlides');
        
        $row->load($slideid);
        
        $upload_dir = config::UPLOAD_DIR.DS."projects".DS.$projectid.DS."slideshows".DS.$row->slideshowid.DS;
        if (!unlink($upload_dir.$row->filename) || !unlink($upload_dir."thumb".DS.$row->filename)) {
            $this->_error[] = _LANG_MEETINGS_SLIDE_DELETE_ERROR;
            return false;
        }
        else {
            $query = "DELETE FROM #__slideshows_slides WHERE id = ".$slideid;
            if (PHPFrame::DB()->query($query) === false) {
                $this->_error[] = PHPFrame::DB()->getLastError();
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
        $fileids = PHPFrame::DB()->loadResultArray($query);
        
        // Get files data
        $files = array();
        for ($i=0; $i<count($fileids); $i++) {
            $query = "SELECT * ";
            $query .= " FROM #__files ";
            $query .= " WHERE id = ".$fileids[$i];
            $files[$i] = PHPFrame::DB()->loadObject($query);
        }
        
        return $files;
    }
    
    public function saveFiles($meetingid, $fileids) {
        if (empty($meetingid)) {
            $this->_error[] = _LANG_PROJECTS_MEETINGS_NO_MEETING_SELECTED;
            return false;
        }
        
        if (!is_array($fileids) && is_int($fileids)) {
            $fileids[] = $fileids;
        }
        
        $query = "DELETE FROM #__meetings_files WHERE meetingid = ".$meetingid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        if (is_array($fileids) && count($fileids) > 0) {
            foreach ($fileids as $fileid) {
                $query = "INSERT INTO #__meetings_files (`id`, `meetingid`, `fileid`) VALUES (NULL, ".$meetingid.", ".$fileid.")";
                if (!PHPFrame::DB()->query($query)) {
                    $this->_error[] = PHPFrame::DB()->getLastError();
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Get list of assignees
     *
     * @param    int        $meetingid
     * @param    bool    $asoc
     * @return    array    Array containing assignees ids or asociative array with id, name and email if asoc is true.
     */
    public function getAssignees($meetingid, $asoc=true) {
        $query = "SELECT um.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_meetings AS um ";
        $query .= "LEFT JOIN #__users u ON u.id = um.userid";
        $query .= " WHERE um.meetingid = ".$meetingid;
        $assignees = PHPFrame::DB()->loadObjectList($query);
        
        // Prepare assignee data
        for ($i=0; $i<count($assignees); $i++) {
            if ($asoc === false) {
                $new_assignees[$i] = $assignees[$i]->userid;
            }
            else {
                $new_assignees[$i]['id'] = $assignees[$i]->userid;
                $new_assignees[$i]['name'] = PHPFrame_User_Helper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
                $new_assignees[$i]['email'] = $assignees[$i]->email;
            }
        }
        
        return $new_assignees;
    }
}
