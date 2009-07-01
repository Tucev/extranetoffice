<?php
/**
 * src/components/com_projects/models/messages.php
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
class projectsModelMessages extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project these messages belong to
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
     * Get messages
     * 
     * @param string $orderby
     * @param string $orderdir
     * @param int    $limit
     * @param int    $limitstart
     * @param string $search
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    public function getCollection(
        $orderby='m.date_sent',
        $orderdir='DESC',
        $limit=25,
        $limitstart=0,
        $search=''
    ) {
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select(array("m.*", "u.username AS created_by_name"))
             ->from("#__messages AS m")
             ->join("JOIN #__users u ON u.id = m.userid")
             ->where("m.projectid", "=", ":projectid")
             ->params(":projectid", $this->_project->id)
             ->groupby("m.id")
             ->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
             
        if ($search) {
            $rows->where("m.subject", "LIKE", ":search")
                 ->params(":search", "%".$search."%");
        }
        
        $rows->load();
        
        // Prepare rows and add relevant data
        if ($rows->countRows() > 0) {
            foreach ($rows as $row) {
                // Get assignees
                $row->assignees = $this->getAssignees($row->id);
                
                // get total comments
                $modelComments = PHPFrame_MVC_Factory::getModel(
                				 	'com_projects', 
                				 	'comments', 
                                    array($this->_project)
                                 );
                                 
                $row->comments = $modelComments->getTotalComments($row->id, 'messages');
            }
        }
        
        return $rows;
    }
    
    public function getRow($messageid)
    {
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select(array("m.*", "u.username AS created_by_name"))
               ->from("#__messages AS m")
               ->join("JOIN #__users u ON u.id = m.userid")
               ->where("m.id", "=", ":messageid")
               ->params(":messageid", $messageid);
        
        $row = new PHPFrame_Database_Row("#__messages");
        
        $row->load($id_obj);
        
        // Get assignees
        $row->assignees = $this->getAssignees($messageid);
        
        // Get comments
        $modelComments = PHPFrame_MVC_Factory::getModel(
        				 	'com_projects', 
        				 	'comments',
                            array($this->_project)
                         );
                         
        $row->comments = $modelComments->getCollection('messages', $messageid);
        
        return $row;
    }
    
    /**
     * Save a project message
     * 
     * @param array $post The array to be used for binding to the row before storing it. 
     *                    Normally the HTTP_POST array.
     *                    
     * @access public
     * @return PHPFrame_Database_Row
     * @since  10
     */
    public function saveRow($post)
    {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = new PHPFrame_Database_Row("#__messages");
        
        $row->bind($post);
        
        $messageid = $row->id;
        if (empty($messageid)) {
            $row->set("userid", PHPFrame::Session()->getUser()->id);
            $row->set("date_sent", date("Y-m-d H:i:s"));
            $row->set("status", '1');
        }
    
        $row->store();
        
        // Delete existing assignees before we store new ones if editing existing issue
        if (!empty($post['id'])) {
            $query = "DELETE FROM #__users_messages WHERE messageid = ".$row->id;
            PHPFrame::DB()->query($query);
        }
        
        // Store assignees
        if (is_array($post['assignees']) && count($post['assignees']) > 0) {
            $query = "INSERT INTO #__users_messages ";
            $query .= " (id, userid, messageid) VALUES ";
            for ($i=0; $i<count($post['assignees']); $i++) {
                if ($i>0) { $query .= ","; }
                $query .= " (NULL, '".$post['assignees'][$i]."', '".$row->id."') ";
            }
            
            PHPFrame::DB()->query($query);
        }
        
        return $row;
    }
    
    /**
     * Delete message
     * 
     * @param int $projectid
     * @param int $messageid
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function deleteRow($messageid)
    {
        //TODO: This function should allow ids as either int or array of ints.
        
        // Delete message's comments
        $query = "DELETE FROM #__comments ";
        $query .= " WHERE projectid = ".$this->_project->id." AND type = 'messages' AND itemid = ".$messageid;
        PHPFrame::DB()->query($query);
        
        // Delete message's assignees
        $query = "DELETE FROM #__users_messages ";
        $query .= " WHERE messageid = ".$messageid;
        PHPFrame::DB()->query($query);
        
        // Instantiate row object
        $row = new PHPFrame_Database_Row("#__messages");
        
        // Delete row from database
        $row->delete($messageid);
    }
    
    /**
     * Get list of assignees
     *
     * @param int  $messageid
     * @param bool $asoc
     * 
     * @access public
     * @return array Array containing assignees ids or asociative array with id, 
     *               name and email if asoc is true.
     * @since  1.0
     */
    public function getAssignees($messageid, $asoc=true)
    {
        $query = "SELECT um.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_messages AS um ";
        $query .= "LEFT JOIN #__users u ON u.id = um.userid";
        $query .= " WHERE um.messageid = ".$messageid;
        
        $rows = PHPFrame::DB()->fetchObjectList($query);
        
        // Prepare assignee data
        $assignees = array();
        for ($i=0; $i<count($rows); $i++) {
            if ($asoc === false) {
                $assignees[$i] = $rows[$i]->userid;
            } else {
                $assignees[$i]['id'] = $rows[$i]->userid;
                $assignees[$i]['name'] = PHPFrame_User_Helper::fullname_format($rows[$i]->firstname, $rows[$i]->lastname);
                $assignees[$i]['email'] = $rows[$i]->email;
            }
        }
        
        return $assignees;
    }
}
