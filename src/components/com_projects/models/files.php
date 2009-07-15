<?php
/**
 * src/components/com_projects/models/files.php
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
 * projectsModelFiles Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelFiles extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project this files belong to
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
        $orderby='f.ts',
        $orderdir='DESC',
        $limit=25,
        $limitstart=0,
        $search=''
    ) {
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select(array("f.*", "u.username AS created_by_name"))
             ->from("#__files AS f")
             ->join("JOIN #__users u ON u.id = f.userid")
             ->join("INNER JOIN (SELECT MAX(id) AS id FROM #__files GROUP BY parentid) ids ON f.id = ids.id")
             ->where("f.projectid", "=", ":projectid")
             ->params(":projectid", $this->_project->id)
             ->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
        
        if ($search) {
            $rows->where("f.title", "LIKE", ":search")
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
                $row->comments = $modelComments->getTotalComments($row->id, 'files');
                    
                // Get older revisions
                $row->children = $this->_getOlderRevisions($row->parentid, $row->id);
            }
        }
        
        return $rows;
    }
    
    /**
     * Get a row object representing the file in the db table
     * 
     * @param int $fileid
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function getRow($fileid)
    {
        // Build SQL query to get row using IdObject
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select(array("f.*", "u.username AS created_by_name"))
               ->from("#__files AS f")
               ->join("JOIN #__users u ON u.id = f.userid")
               ->where("f.id", "=", ":fileid")
               ->params(":fileid", $fileid)
               ->orderby("f.ts", "DESC");
        
        // Create instance of row
        $row = new PHPFrame_Database_Row('#__files');
        
        // Load row data using query
        $row->load($id_obj);
        
        // Get assignees
        $row->assignees = $this->getAssignees($fileid);
        
        // Get comments
        $modelComments = PHPFrame_MVC_Factory::getModel(
        									   	  'com_projects', 
        									   	  'comments', 
                                                  array($this->_project)
                                               );
        $row->comments = $modelComments->getCollection('files', $fileid);
        
        // Get older revisions
        $row->children = $this->_getOlderRevisions($row->parentid, $row->id);
                
        return $row;
    }
    
    /**
     * Save a project file
     * 
     * This method uploads a project file and stores the relevant entry in the database.
     * 
     * @param $post The array to be used for binding to the row before storing it.
     *              Normally the HTTP_POST array.
     *              
     * @access public
     * @return mixed  Returns the stored table row object on success or FALSE on failure
     * @since  1.0
     */
    public function saveRow($post)
    {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = new PHPFrame_Database_Row("#__files");
        
        $row->bind($post);
        
        // Generate revision
        $parentid = $row->parentid;
        if (empty($parentid)) {
            $row->set('revision', 0);
        }
        else {
            $sql = "SELECT revision FROM #__files ";
            $sql .= " WHERE parentid = :parentid";
            $sql .= " ORDER BY revision DESC LIMIT 0,1";
            
            $params = array(":parentid"=>$row->parentid);
            
            $db = PHPFrame::DB();
            $current_revision = $db->fetchColumn($sql, $params);
            
            $row->set('revision', ($current_revision+1));
        }
        
        // upload the file
        $upload_dir = PHPFRAME_VAR_DIR.DS."projects".DS.$post['projectid'].DS."files";
        PHPFrame_Utils_Filesystem::ensureWritableDir($upload_dir);
        $accept = PHPFrame::Config()->get("UPLOAD_ACCEPT"); // mime types
        $max_upload_size = PHPFrame::Config()->get("MAX_UPLOAD_SIZE")*(1024*1024); // Mb
        $file = PHPFrame_Utils_Filesystem::uploadFile(
                                               'filename',
                                               $upload_dir, 
                                               $accept, 
                                               $max_upload_size
                                           );
        
        if (!empty($file['error'])) {
            $this->_error[] = $file['error'];
            return false;
        }
        
        $row->set('filename', $file['file_name']);
        $row->set('filesize', $file['file_size']);
        $row->set('mimetype', $file['file_type']);
        
        $row->set('userid', PHPFrame::Session()->getUser()->id);
        $row->set("ts", date("Y-m-d H:i:s"));
        
        $row->store();
        
        // Make parent files have their own id as their parentid
        $parentid = $row->parentid;
        if (empty($parentid)) {
            $row->set('parentid', $row->id);
            $row->store();
        }
        
        // File assignees are stored with a reference to the parentid, 
        // so they are assigned to the thread rather than individual files
        // Delete existing assignees before we store new ones if editing existing issue
        if ($row->revision > 0) {
            $query = "DELETE FROM #__users_files WHERE fileid = ".$row->parentid;
            PHPFrame::DB()->query($query);
        }
        
        // Store assignees
        if (is_array($post['assignees']) && count($post['assignees']) > 0) {
            $query = "INSERT INTO #__users_files ";
            $query .= " (id, userid, fileid) VALUES ";
            for ($i=0; $i<count($post['assignees']); $i++) {
                if ($i>0) { $query .= ","; }
                $query .= " (NULL, '".$post['assignees'][$i]."', '".$row->parentid."') ";
            }
            
            PHPFrame::DB()->query($query);
        }
        
        return $row;
    }
    
    /**
     * Delete file row from database and file from filesystem
     * 
     * @param int $fileid The id of the file to delete
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function deleteRow($fileid)
    {
        //TODO: This function should allow ids as either int or array of ints.
        //TODO: This function should delete related items if any (comments, ...)
        
        // Instantiate table object    
        $row = new PHPFrame_Database_Row("#__files");
        
        // Load row data
        $row->load($fileid);
        
        // Delete file from filesystem
        $file_path = PHPFRAME_VAR_DIR.DS."projects".DS.$row->projectid;
        $file_path .= DS."files".DS.$row->filename;
        unlink($file_path);
        
        // Delete row from database
        $row->delete($fileid);
    }
    
    /**
     * Download a given project file
     * 
     * @param int $fileid The id of the file to download
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function downloadFile($fileid)
    {       
        $row = new PHPFrame_Database_Row("#__files");
        
        // Load row data
        $row->load($fileid);
        
        header("Content-type: ".$row->mimetype);
        header("Content-Disposition: attachment; filename=\"".$row->filename."\"");        
        header("Content-Length: ".$row->filesize);
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        ob_clean();
        flush();
        
        // begin download
        $path_to_file = PHPFRAME_VAR_DIR.DS."projects".DS.$row->projectid;
        $path_to_file .= DS."files".DS.$row->filename;
        $this->_readfile_chunked($path_to_file);
        exit;
    }
    
    /**
     * Get list of assignees
     *
     * @param    int        $fileid
     * @param    bool    $asoc
     * @return    array    Asociative array with userid, name and email of assignees
     */
    public function getAssignees($fileid, $asoc=true)
    {
        $query = "SELECT uf.userid, u.firstname, u.lastname, u.email";
        $query .= " FROM #__users_files AS uf ";
        $query .= "LEFT JOIN #__users u ON u.id = uf.userid";
        $query .= " WHERE uf.fileid = ".$fileid;
        
        $rows = PHPFrame::DB()->fetchObjectList($query);
        
        // Prepare assignee data
        $assignees = array();
        for ($i=0; $i<count($rows); $i++) {
            if ($asoc === false) {
                $assignees[$i] = $rows[$i]->userid;
            }
            else {
                $assignees[$i]['id'] = $rows[$i]->userid;
                $assignees[$i]['name'] = PHPFrame_User_Helper::fullname_format($rows[$i]->firstname, $rows[$i]->lastname);
                $assignees[$i]['email'] = $rows[$i]->email;
            }
        }
        
        return $assignees;
    }
    
    private function _readfile_chunked($filename, $retbytes=true)
    {
       $chunksize = 1*(1024*1024); // how many bytes per chunk
       $buffer = '';
       $cnt =0;
       $handle = fopen($filename, 'rb');
       if ($handle === false) {
           return false;
       }
       while (!feof($handle)) {
           $buffer = fread($handle, $chunksize);
           echo $buffer;
           ob_flush();
           flush();
           if ($retbytes) {
               $cnt += strlen($buffer);
           }
       }
           $status = fclose($handle);
       if ($retbytes && $status) {
           return $cnt; // return num. bytes delivered like readfile() does.
       }
       return $status;
    }
    
    private function _getOlderRevisions($parentid, $id)
    {
        // get children (older revisions)
        $query = "SELECT f.*, u.username AS created_by_name ";
        $query .= " FROM #__files AS f ";
        $query .= " JOIN #__users u ON u.id = f.userid";
        $query .= " WHERE f.parentid = ".$parentid." AND f.id <> ".$id;
        $query .= " ORDER BY f.ts DESC";
        
        return PHPFrame::DB()->fetchObjectList($query);
    }
}
