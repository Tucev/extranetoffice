<?php
/**
 * src/components/com_projects/models/projects.php
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
 * projectsModelProjects Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelProjects extends PHPFrame_MVC_Model
{
    /**
     * Constructor
     *
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct() {}
    
    /**
     * Get projects.
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
        $orderby="p.created", 
        $orderdir="DESC", 
        $limit=25, 
        $limitstart=0, 
        $search=""
    ) {
        $userid = PHPFrame::Session()->getUserId();
        
        // Build select fields array
        $select = array("p.*", 
                        "u.username AS created_by_name", 
                        "pt.name AS project_type_name");
        
        // Create row collection object
        // Show only public projects or projects where user has an assigned role
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select($select)
             ->from("#__projects AS p")
             ->join("JOIN #__users u ON u.id = p.created_by")
             ->join("LEFT JOIN #__project_types pt ON pt.id = p.project_type")
             ->where("p.access = '0'", 
                     "OR", 
                     "(".$userid
                        ." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) )");
        // Add search filtering
        if ($search) {
            $rows->where("p.name", "LIKE", ":search")
                 ->params(":search", "%".$search."%");
        }
        
        $rows->groupby("p.id")
             ->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
        
        $rows->load();
        
        return $rows;
    }
    
    /**
     * Get project as database row
     * 
     * @param int $projectid The id of the project we want to get from the db
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function getRow($projectid)
    {
        $userid = PHPFrame::Session()->getUserId();
        
        // Build SQL query to get row using IdObject
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select(array("p.*", "u.username AS created_by_name", "pt.name AS project_type_name"))
               ->from("#__projects AS p")
               ->join("JOIN #__users u ON u.id = p.created_by")
               ->join("LEFT JOIN #__project_types pt ON pt.id = p.project_type")
               ->where("p.id", "=", ":projectid")
               ->where("p.access = '0'", 
                       "OR", "(:userid"
                                ." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) )")
               ->params(":projectid", $projectid)
               ->params(":userid", $userid)
               ->groupby("p.id");
        
        // Create instance of row
        $row = new PHPFrame_Database_Row('#__projects');
        
        // Load row data using query and return
        return $row->load($id_obj);
    }
    
    
    /**
     * Save a project sent in the request.
     * 
     * @param array $post The array to be used for binding to the row before storing it.
     *                    Normally the HTTP_POST array.
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function saveRow($post)
    {
        // Instantiate table object
        $row = new PHPFrame_Database_Row('#__projects');
        
        // Bind the post data to the row array (exluding created and created_by)
        $row->bind($post, 'created,created_by');
        
        // Manually set created by and created date for new records
        if (empty($row->id)) {
            $row->set('created', date("Y-m-d H:i:s"));
            $row->set('created_by', PHPFrame::Session()->getUserId());
        }
        
        // Store row and return row object
        $row->store();
        
        // Create filesystem directories if they don't exist yet
        PHPFrame_Utils_Filesystem::ensureWritableDir(PHPFRAME_VAR_DIR.DS."projects");
        PHPFrame_Utils_Filesystem::ensureWritableDir(PHPFRAME_VAR_DIR.DS."projects".DS.$row->id);
        PHPFrame_Utils_Filesystem::ensureWritableDir(_ABS_PATH.DS."public".DS.PHPFrame::Config()->get("UPLOAD_DIR").DS."projects");
        PHPFrame_Utils_Filesystem::ensureWritableDir(_ABS_PATH.DS."public".DS.PHPFrame::Config()->get("UPLOAD_DIR").DS."projects".DS.$row->id);
        
        return $row;
    }
    
    /**
     * Delete a project by id
     * 
     * @todo Before deleting the project we need to delete all its issue, files, ...
     * 
     * @param int $projectid The id of the project we want to delete
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function deleteRow($projectid)
    {
        // Instantiate table object
        $row = new PHPFrame_Database_Row('#__projects');
        
        // Delete row from database
        $row->delete($projectid);
    }
}
