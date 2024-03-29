<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * projectsModelMilestones Class
 * 
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_Model
 */
class projectsModelMilestones extends PHPFrame_MVC_Model {
    /**
     * Constructor
     *
     * @since 1.0.1
     */
    function __construct() {}
    
    /**
     * Gets milestones list info
     * 
     * @param    object    $list_filter    Object of type PHPFrame_Database_CollectionFilter
     * @param    int        $projectid
     * @return    array
     */
    public function getMilestones(PHPFrame_Database_CollectionFilter $list_filter, $projectid) {
        $where = array();
        
        // Show only public projects or projects where user has an assigned role
        //TODO: Have to apply access levels
        //$where[] = "( p.access = '0' OR (".PHPFrame::Session()->getUser()->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
        
        $search = $list_filter->getSearchStr();
        if ( $search ) {
            $where[] = "m.title LIKE '%".PHPFrame::DB()->getEscaped($search)."%'";
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
                  FROM #__milestones AS m 
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
        
        $rows = PHPFrame::DB()->fetchObjectList($query);
        
        // Prepare rows and add relevant data
        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                // Get assignees
                $row->assignees = $this->getAssignees($row->id);
                
                // get total comments
                $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
                $row->comments = $modelComments->getTotalComments($row->id, 'milestones');
                
                // Sort out status according to due date
                if ($row->due_date < date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
                    $row->due_date_class = 'overdue';
                    $row->status = _LANG_STATUS_OVERDUE;
                }
                elseif ($row->due_date > date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
                    $row->due_date_class = 'open';
                    $row->status = _LANG_STATUS_UPCOMING;
                }
                elseif ($row->closed != '0000-00-00 00:00:00') {
                    $row->due_date_class = 'closed';
                    $row->status = _LANG_STATUS_CLOSED;
                }
            }
        }
        
        return $rows;
    }
    
    /**
     * Gets detailed milestone information
     *
     * @param int $projectid
     * @param int $milestoneid
     * @return object table row containing milestone info
     */
    public function getMilestonesDetail($projectid, $milestoneid) {
        $query = "SELECT m.*, u.username AS created_by_name ";
        $query .= " FROM #__milestones AS m ";
        $query .= " JOIN #__users u ON u.id = m.created_by ";
        $query .= " WHERE m.id = ".$milestoneid;
        $query .= " ORDER BY m.due_date DESC";
        $row = PHPFrame::DB()->loadObject($query);
        
        // Sort out status according to due date
        if ($row->due_date < date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
            $row->due_date_class = 'overdue';
            $row->status = _LANG_STATUS_OVERDUE;
        }
        elseif ($row->due_date > date("Y-m-d H:i:s") && $row->closed == '0000-00-00 00:00:00') {
            $row->due_date_class = 'open';
            $row->status = _LANG_STATUS_UPCOMING;
        }
        elseif ($row->closed != '0000-00-00 00:00:00') {
            $row->due_date_class = 'closed';
            $row->status = _LANG_STATUS_CLOSED;
        }
        
        // Get assignees
        $row->assignees = $this->getAssignees($milestoneid);
        
        // Get comments
        $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
        $row->comments = $modelComments->getComments($projectid, 'milestones', $milestoneid);
        
        return $row;
    }
    
    /**
     * Save a project milestone
     * 
     * @param    $post    The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
     * @return    mixed    Returns the stored table row object on success or FALSE on failure
     */
    public function saveMilestone($post) {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = $this->getTable('milestones');
        
        if (!$row->bind($post)) {
            $this->_error[] = $row->getLastError();
            return false;
        }
        
        if (empty($row->id)) {
            $row->created_by = PHPFrame::Session()->getUser()->id;
            $row->created = date("Y-m-d H:i:s");
        }
        else {
            $row->load($post['id']);
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
            $query = "DELETE FROM #__users_milestones WHERE milestoneid = ".$row->id;
            PHPFrame::DB()->query($query);
        }
        
        // Store assignees
        if (is_array($post['assignees']) && count($post['assignees']) > 0) {
            $query = "INSERT INTO #__users_milestones ";
            $query .= " (id, userid, milestoneid) VALUES ";
            for ($i=0; $i<count($post['assignees']); $i++) {
                if ($i>0) { $query .= ","; }
                $query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
            }
            
            PHPFrame::DB()->query($query);
        }
        
        return $row;
    }
    
    /**
     * Delete a milestone
     *
     * @param int $projectid
     * @param int $milestoneid
     * @return bool
     */
    public function deleteMilestone($projectid, $milestoneid) {
        //TODO: This function should allow ids as either int or array of ints.
        //TODO: This function should also check permissions before deleting
        
        // Delete message's comments
        $query = "DELETE FROM #__comments ";
        $query .= " WHERE projectid = ".$projectid." AND type = 'milestones' AND itemid = ".$milestoneid;;
        PHPFrame::DB()->query($query);
        
        // Delete message's assignees
        $query = "DELETE FROM #__users_milestones ";
        $query .= " WHERE milestoneid = ".$milestoneid;
        PHPFrame::DB()->query($query);
        
        // Instantiate table object
        $row = $this->getTable('milestones');
        
        // Delete row from database
        if (!$row->delete($milestoneid)) {
            $this->_error = $row->error;
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Get list of assignees
     *
     * @param    int        $milestoneid
     * @param    bool    $asoc
     * @return    array    Array containing assignees ids or asociative array with id, name and email if asoc is true.
     */
    public function getAssignees($milestoneid, $asoc=true) {
        $query = "SELECT um.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_milestones AS um ";
        $query .= "LEFT JOIN #__users u ON u.id = um.userid";
        $query .= " WHERE um.milestoneid = ".$milestoneid;
        $assignees = PHPFrame::DB()->fetchObjectList($query);
        
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
