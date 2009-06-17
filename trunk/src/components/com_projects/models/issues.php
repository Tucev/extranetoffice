<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsModelIssues Class
 * 
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_Model
 * @todo        check for NULL values rather than 00-00-000 00:00
 */
class projectsModelIssues extends PHPFrame_MVC_Model {
    /**
     * Constructor
     *
     * @since    1.0
     */
    function __construct() {}
    
    /**
     * This method gets issues for specified project
     * 
     * @param    object    $list_filter    Object of type PHPFrame_Database_CollectionFilter
     * @param     int     $projectid
     * @param     bool    $overdue        If set to true it only returns overdue issues
     * @return    array
     */
    public function getIssues(PHPFrame_Database_CollectionFilter $list_filter, $projectid, $overdue=false) {
        $filter_status = PHPFrame::Request()->get('filter_status', 'all');
        $filter_assignees = PHPFrame::Request()->get('filter_assignees', 'me');

        $where = array();
        
        // Show only public projects or projects where user has an assigned role
        $where[] = "( i.access = '0' OR (".PHPFrame::Session()->getUser()->id." IN (SELECT userid FROM #__users_issues WHERE issueid = i.id) ) )";
        
        // Add search filtering
        $search = $list_filter->getSearchStr();
        if ($search) {
            $where[] = "i.title LIKE '%".PHPFrame::DB()->getEscaped($list_filter->getSearchStr())."%'";
        }
        
        if (!empty($projectid)) {
            $where[] = "i.projectid = ".$projectid;    
        }
        
        if ($overdue === true) {
            $where[] = "i.dtend < '".date("Y-m-d")." 23:59:59'";
            $where[] = "i.closed = '0000-00-00 00:00:00'";    
        }
        
        if ($filter_status == 'open' && $overdue !== true) {
            $where[] = "i.closed = '0000-00-00 00:00:00'";    
        }
        elseif ($filter_status == 'closed' && $overdue !== true) {
            $where[] = "i.closed <> '0000-00-00 00:00:00'";    
        } 
        
        if ($filter_assignees) {
            //$where[] = "i.projectid = "
        }

        $where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

        // get the total number of records
        // This query groups the files by parentid so and retireves the latest revision for each file in current project
        $query = "SELECT 
                  i.*, 
                  u.username AS created_by_name 
                  FROM #__issues AS i 
                  JOIN #__users u ON u.id = i.created_by "
                  . $where . 
                  " GROUP BY i.id ";
        //echo str_replace('#__', 'eo_', $query); exit;
        
        // Run query to get total rows before applying filter
        $list_filter->setTotal(PHPFrame::DB()->query($query)->rowCount());

        // Add order by and limit statements for subset (based on filter)
        //$query .= $list_filter->getOrderBySQL();
        $query .= $list_filter->getLimitSQL();
        //echo str_replace('#__', 'eo_', $query); exit;
        
        $rows = PHPFrame::DB()->loadObjectList($query);
        
        // Prepare rows and add relevant data
        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                // Get assignees
                $row->assignees = $this->getAssignees($row->id);
                
                // get total comments
                $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
                $row->comments = $modelComments->getTotalComments($row->id, 'issues');
                
                // set status
                if ($row->closed != "0000-00-00 00:00:00") {
                    $row->status = "closed";
                }
                elseif ($row->dtend < date("Y-m-d")." 23:59:59") {
                    $row->status = "overdue";
                }
                else {
                    $row->status = "open";
                }
            }
        }
        
        return $rows;
    }
    
    /**
     * Get issues detail
     *
     * @param int $projectid
     * @param int $issueid
     * @return mixed returns $row on success and FALSE on failure
     */
    public function getIssuesDetail($projectid, $issueid) {
        $query = "SELECT i.*, u.username AS created_by_name ";
        $query .= " FROM #__issues AS i ";
        $query .= " JOIN #__users u ON u.id = i.created_by ";
        $query .= " WHERE i.id = ".$issueid;
        $query .= " ORDER BY i.created DESC";
        
        $row = PHPFrame::DB()->loadObject($query);
        
        // Get assignees
        $row->assignees = $this->getAssignees($issueid);
        
        // Get comments
        $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
        $row->comments = $modelComments->getComments($projectid, 'issues', $issueid);
        
        return $row;
    }
    
    /**
     * Save a project issue
     * 
     * @param    $post    The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
     * @return    mixed    Returns the stored table row object on success or FALSE on failure
     */
    public function saveIssue($post) {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
            
        $row = $this->getTable('issues');
        
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
            $query = "DELETE FROM #__users_issues WHERE issueid = ".$row->id;
            PHPFrame::DB()->query($query);
        }
        
        // Store assignees
        if (is_array($post['assignees']) && count($post['assignees']) > 0) {
            $query = "INSERT INTO #__users_issues ";
            $query .= " (id, userid, issueid) VALUES ";
            for ($i=0; $i<count($post['assignees']); $i++) {
                if ($i>0) { $query .= ","; }
                $query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
            }
            
            PHPFrame::DB()->query($query);
        }
        
        return $row;
    }
    
    /**
     * Delete issue
     *
     * @param    int        $projectid    The project id.
     * @param    int        $issueid    The id of the issue we want to delete.
     * @return    bool    Returns TRUE on success or FALSE on error
     */
    public function deleteIssue($projectid, $issueid) {
        //TODO: This function should allow ids as either int or array of ints.
        //TODO: This function should also check permissions before deleting
        
        // Delete message's comments
        $query = "DELETE FROM #__comments ";
        $query .= " WHERE projectid = ".$projectid." AND type = 'issues' AND itemid = ".$issueid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        // Delete message's assignees
        $query = "DELETE FROM #__users_issues ";
        $query .= " WHERE issueid = ".$issueid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        // Instantiate table 
        $row =& PHPFrame_Base_Singleton::getInstance("projectsTableIssues");
        
        // Delete row from database
        if (!$row->delete($issueid)) {
            $this->_error[] = $row->getLastError();
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Close an issue
     *
     * @param    int        $projectid
     * @param    int        $issueid
     * @return    mixed    Returns issues row object or FALSE on failure.
     */
    public function closeIssue($projectid, $issueid) {
        $query = "UPDATE #__issues ";
        $query .= " SET closed = '".date("Y-m-d H:i:s")."' WHERE id = ".$issueid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        $row =& PHPFrame_Base_Singleton::getInstance("projectsTableIssues");
        $row->load($issueid);
        return $row;
    }
    
    /**
     * Reopen an issue
     *
     * @param    int        $projectid
     * @param    int        $issueid
     * @return    mixed    Returns issues row object or FALSE on failure.
     */
    public function reopenIssue($projectid, $issueid) {
        $query = "UPDATE #__issues ";
        $query .= " SET closed = '0000-00-00 00:00:00' WHERE id = ".$issueid;
        if (!PHPFrame::DB()->query($query)) {
            $this->_error[] = PHPFrame::DB()->getLastError();
            return false;
        }
        
        $row =& PHPFrame_Base_Singleton::getInstance("projectsTableIssues");
        $row->load($issueid);
        return $row;
    }
    
    /**
     * Get issue count
     *
     * @param    int        $projectid
     * @param    bool    $overdue
     * @return    mixed    Returns numeric resource or FALSE on failure.
     */
    public function getTotalIssues($projectid, $overdue=false) {
        $query = "SELECT COUNT(id) FROM #__issues ";
        $query .= " WHERE projectid = ".$projectid;
        if ($overdue === true) { 
            $query .= " AND dtend < '".date("Y-m-d")." 23:59:59' AND closed = '0000-00-00 00:00:00'"; 
        }
        
        return PHPFrame::DB()->loadResult($query);
    }
    
    /**
     * Get list of assignees
     *
     * @param    int        $issueid
     * @param    bool    $asoc
     * @return    array    Array containing assignees ids or asociative array with id, name and email if asoc is true.
     */
    public function getAssignees($issueid, $asoc=true) {
        $query = "SELECT ui.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_issues AS ui ";
        $query .= "LEFT JOIN #__users u ON u.id = ui.userid";
        $query .= " WHERE ui.issueid = ".$issueid;
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
