<?php
/**
 * src/components/com_projects/models/comments.php
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
 * projectsModelComments Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelComments extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project this comments belong to
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
     * Get collection of comment rows from database
     * 
     * @param string $type
     * @param int    $itemid
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    function getCollection($type, $itemid)
    {
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select(array("c.*", "u.username AS created_by_name"))
             ->from("#__comments AS c")
             ->join("JOIN #__users u ON u.id = c.userid")
             ->where("c.projectid", "=", ":projectid")
             ->params(":projectid", $this->_project->id)
             ->where("c.type", "=", ":type")
             ->params(":type", $type)
             ->where("c.itemid", "=", ":itemid")
             ->params(":itemid", $itemid)
             ->orderby("c.created", "DESC"); 
        
        $rows->load();
        
        return $rows;
    }
    
    /**
     * Save a project comment
     * 
     * @param  $post The array to be used for binding to the row before storing it. 
     *               Normally the HTTP_POST array.
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function saveRow($post)
    {
        // Check whether a project id is included in the post array
        if (empty($post['projectid'])) {
            $this->_error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
            return false;
        }
        
        $row = new PHPFrame_Database_Row("#__comments");
        
        $row->bind($post);
        
        $row->set("userid", PHPFrame::Session()->getUserId());
        $row->set("created", date("Y-m-d H:i:s"));
    
        $row->store();
        
        return $row;
    }
    
    /**
     * Delete comment
     * 
     * @param int $commentid
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    function deleteRow($commentid)
    {
        //TODO: This function should allow ids as either int or array of ints.
        
        // Instantiate table object
        $row = new PHPFrame_Database_Row("#__comments");
        
        // Delete row from database
        $row->delete($commentid);
    }
    
    function itemid2title($itemid, $type)
    {
        switch ($type) {
            case 'files' :
                $sql = "SELECT title FROM #__files";;
                break;
            case 'issues' :
                $sql = "SELECT title FROM #__issues";
                break;
            case 'meetings' :
                $sql = "SELECT name FROM #__meetings";
                break;
            case 'messages' :
                $sql = "SELECT subject FROM #__messages";
                break;
            case 'milestones' :
                $sql = "SELECT title FROM #__milestones";
                break;
        }
        
        $sql .= " WHERE id = :id";
        
        return PHPFrame::DB()->fetchColumn($sql, array(":id"=>$itemid));
    }
    
    function getTotalComments($itemid, $type)
    {
        $sql = "SELECT COUNT(id) FROM #__comments ";
        $sql .= " WHERE itemid = :itemid AND type = :type";
        
        $params = array(":itemid"=>$itemid, ":type"=>$type);
        
        return PHPFrame::DB()->fetchColumn($sql, $params);
    }
    
    function fetchCommentsFromEmail()
    {
        $imap = new PHPFrame_Mail_IMAP(
                        config::IMAP_HOST, 
                        config::IMAP_PORT, 
                        config::IMAP_USER, 
                        config::IMAP_PASSWORD
                    );
                    
        $messages = $imap->getMessages();
        $imap->close();
        
        foreach ($messages as $message) {
            //var_dump($message);
            // Get data appended to message id
            preg_match("/\-(.*)@/i", $message->in_reply_to, $matches);
            parse_str(base64_decode($matches[1]), $data);
            // Get from address
            preg_match("/<(.*)>/", $message->from, $matches);
            $data['fromaddress'] = $matches[1];
            
            // Only process messages from com_projects
            if ($data['c'] == 'com_projects') {
                $message->data = $data;
                $comments_messages[] = $message;
            }
            unset($data);
        }
        
        return $comments_messages;
    }
}
