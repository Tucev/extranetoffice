<?php
/**
 * src/components/com_projects/models/issues.php
 * 
 * PHP version 5
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
 */

/**
 * projectsModelIssues Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelIssues extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project this issues belongs to
     * 
     * @var object
     */
    private $_project=null;
    
    /**
     * Constructor
     * 
     * @param object $project
     *
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct($project)
    {
        $this->_project = $project;
    }
    
    /**
     * This method gets issues for specified project
     * 
     * @param string $orderby
     * @param string $orderdir
     * @param int    $limit
     * @param int    $limitstart
     * @param string $search
     * @param bool   $overdue    If set to true it only returns overdue issues
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    public function getCollection(
        $orderby="i.dtstart", 
        $orderdir="DESC", 
        $limit=25, 
        $limitstart=0, 
        $search="",
        $overdue=false
    ) {
        $filter_status = PHPFrame::Request()->get('filter_status', 'all');
        $filter_assignees = PHPFrame::Request()->get('filter_assignees', 'me');
        $userid = PHPFrame::Session()->getUserId();
        
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select(array("i.*", "u.username AS created_by_name"))
             ->from("#__issues AS i")
             ->join("JOIN #__users u ON u.id = i.created_by")
             ->where("i.projectid", "=", $this->_project->id)
             ->where("i.access = '0'", 
                     "OR", 
                     "(".$userid
                        ." IN (SELECT userid FROM #__users_issues WHERE issueid = i.id))")
             ->groupby("i.id")
             ->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
             
        // Add search filtering
        if ($search) {
            $rows->where("i.title", "LIKE", ":search")
                 ->params(":search", "%".$search."%");
        }
        
        // Filter overdue only
        if ($overdue === true) {
            $rows->where("i.dtend", "<", "'".date("Y-m-d")." 23:59:59'")
                 ->where("i.closed", "=", "'0000-00-00 00:00:00'");   
        }
        
        // Filter by status
        if ($filter_status == 'open' && $overdue !== true) {
            $rows->where("i.closed", "=", "'0000-00-00 00:00:00'");  
        } elseif ($filter_status == 'closed' && $overdue !== true) {
            $rows->where("i.closed", "<>", "'0000-00-00 00:00:00'");    
        }
        
        $rows->load();
        
        // Prepare rows and add relevant data
        if ($rows->countRows() > 0) {
            foreach ($rows as $row) {
                // Get assignees
                $row->assignees = $this->getAssignees($row->id);
                
                // get total comments
                $modelComments = PHPFrame_MVC_Factory::getModel('com_projects', 'comments');
                $row->comments = $modelComments->getTotalComments($row->id, 'issues');
                
                // set status
                if ($row->closed != "0000-00-00 00:00:00") {
                    $row->status = "closed";
                } elseif ($row->dtend < date("Y-m-d")." 23:59:59") {
                    $row->status = "overdue";
                } else {
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
    public function getIssuesDetail($projectid, $issueid)
    {
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
     * @param $post The array to be used for binding to the row before storing it. 
     *              Normally the HTTP_POST array.
     * 
     * @access public
     * @return mixed  Returns the stored table row object on success or FALSE on failure
     * @since  1.0
     */
    public function saveIssue($post)
    {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
            
        $row = new PHPFrame_Database_Row("#__issues");
        
        if (empty($post['id'])) {
            $row->set("created_by", PHPFrame::Session()->getUserId());
            $row->set("created", date("Y-m-d H:i:s"));
        } else {
            $row->load($post['id']);
        }
        
        // Bind the post data to the row array (exluding created and created_by)
        $row->bind($post, 'created,created_by');
        
        // Store row
        $row->store();
        
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
    public function deleteIssue($projectid, $issueid)
    {
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
        } else {
            return true;
        }
    }
    
    /**
     * Close an issue
     *
     * @param int $issueid The id of the issue we want to close
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function closeIssue($issueid)
    {
        $row = new PHPFrame_Database_Row("#__issues");
        $row->load($issueid);
        $row->set("closed", date("Y-m-d H:i:s"));
        $row->store();
        
        return $row;
    }
    
    /**
     * Reopen an issue
     *
     * @param int $issueid The id of the issue we want to close
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function reopenIssue($issueid)
    {
        $row = new PHPFrame_Database_Row("#__issues");
        $row->load($issueid);
        $row->set("closed", "0000-00-00 00:00:00");
        $row->store();
        
        return $row;
    }
    
    /**
     * Get issue count
     *
     * @param    int        $projectid
     * @param    bool    $overdue
     * @return    mixed    Returns numeric resource or FALSE on failure.
     */
    public function getTotalIssues($projectid, $overdue=false)
    {
        $sql = "SELECT COUNT(id) FROM #__issues ";
        $sql .= " WHERE projectid = :projectid";
        if ($overdue === true) { 
            $sql .= " AND dtend < '".date("Y-m-d")." 23:59:59' AND closed = '0000-00-00 00:00:00'"; 
        }
        
        return PHPFrame::DB()->fetchColumn($sql, array(":projectid"=>$projectid));
    }
    
    /**
     * Get list of assignees
     *
     * @param    int        $issueid
     * @param    bool    $asoc
     * @return    array    Array containing assignees ids or asociative array with id, name and email if asoc is true.
     */
    public function getAssignees($issueid, $asoc=true)
    {
        $query = "SELECT ui.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_issues AS ui ";
        $query .= "LEFT JOIN #__users u ON u.id = ui.userid";
        $query .= " WHERE ui.issueid = ".$issueid;
        $assignees = PHPFrame::DB()->fetchObjectList($query);
        
        // Prepare assignee data
        for ($i=0; $i<count($assignees); $i++) {
            if ($asoc === false) {
                $new_assignees[$i] = $assignees[$i]->userid;
            } else {
                $new_assignees[$i]['id'] = $assignees[$i]->userid;
                $new_assignees[$i]['name'] = PHPFrame_User_Helper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
                $new_assignees[$i]['email'] = $assignees[$i]->email;
            }
        }
        
        return $new_assignees;
    }
}
