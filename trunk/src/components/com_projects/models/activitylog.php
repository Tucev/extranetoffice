<?php
/**
 * src/components/com_projects/models/activity.php
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
 * projectsModelActivitylog Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @since      1.0
 */
class projectsModelActivitylog extends PHPFrame_MVC_Model
{
    /**
     * A reference to the project this activity log belongs to
     * 
     * @var object
     */
    private $_project=null;
    
    /**
     * Constructor
     * 
     * @param PHPFrame_Database_Row $project The project row this activity 
     *                                       log belongs to.
     *
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct(PHPFrame_Database_Row $project)
    {
        $this->_project = $project;
    }
    
    /**
     * Get a collection of activitylog rows
     * 
     * @access public
     * @return PHPFrame_Database_RowCollection
     * @since  1.0
     */
    public function getCollection(
        $orderby="ts", 
        $orderdir="DESC", 
        $limit=25, 
        $limitstart=0
    ) {
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select("*")
             ->from("#__activitylog")
             ->where("projectid", "=", $this->_project->id)
             ->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
        
        $rows->load();
        
        return $rows;
    }
    
    /**
     * This function stores a new activity log entry and notifies the assignees 
     * if necessary
     *
     * @param string $type        Values = 'issues', 'files', 'messages', 
     *                            'meetings', 'milestones'
     * @param string $description The log message
     * @param string $url         The url to the item
     * @param array  $assignees   An array containing the items assignees.
     * @param bool   $notify      Boolean to indicate whether we want to send a 
     *                            notification via email or not.
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function insertRow(
        $type, 
        $action, 
        $title, 
        $description, 
        $url, 
        $assignees, 
        $notify
    ) {
        // Store notification in db
        $row = new PHPFrame_Database_Row('#__activitylog');
        $row->set('projectid', $this->_project->id);
        $row->set('userid', PHPFrame::Session()->getUser()->id);
        $row->set('type', $type);
        $row->set('action', $action);
        $row->set('title', $title);
        $row->set('description', $description);
        $row->set('url', $url);
        $row->set('ts', date("Y-m-d H:i:s"));
        // Store row in db (this will throw exceptions on errors)
        $row->store();
        
        // Send notifications via email
        if ($notify === true && is_array($assignees) && sizeof($assignees) > 0) {
            $this->_notify($row, $assignees);
        }
        
        return $row;
    }
    
    public function deleteRow()
    {
        throw new Exception("I have to delete an activitylog row... Please finish me!!!");
    }
    
    /**
     * This function is called when we save an activity log
     *
     * @param object $row
     * @param array  $assignees
     * 
     * @access private
     * @return bool    Returns TRUE on success or FALSE on failure
     * @since  1.0
     * @todo   sanatise address, subject & body
     */
    private function _notify($row, $assignees)
    {
        $uri = new PHPFrame_Utils_URI();
        $user = PHPFrame::Session()->getUser();
        
        $subject = "[".$this->_project->name."] ";
        $subject .= $row->action." by ".$user->firstname." ".$user->lastname;
        
        $new_mail = new PHPFrame_Mail_Mailer();
        $new_mail->Subject = $subject;
        $new_mail->Body = sprintf(
                              _LANG_ACTIVITYLOG_NOTIFY_BODY, 
                              $this->_project->name, 
                              $row->action." by ".$user_name,
                              PHPFrame_Utils_Rewrite::rewriteURL($row->url, false), 
                              $row->description
                          );
        
        // Append message id suffix with data to be used when processing replies
        parse_str($row->url, $url_array);
        
        // Find tool keyword (file, issue, message, ...)
        preg_match('/action=get_([a-zA-Z]+)/i', $row->url, $tool_matches);
        
        // Find item id usings tool keyword + id (fileid, issueid, ...)
        $pattern = '/'.$tool_matches[1].'id=([0-9]+)/i';
        preg_match($pattern, $row->url, $matches);
        
        $msg_id_suffix = 'c='.PHPFrame::Request()->getComponentName();
        $msg_id_suffix .= '&p='.$row->projectid.'&t='.$tool_matches[1];
        $msg_id_suffix .= 's&i='.$matches[1];
        $new_mail->setMessageIdSuffix($msg_id_suffix);
        
        // Get assignees email addresses and exclude the user triggering the notification
        $query = "SELECT firstname, lastname, email ";
        $query .= " FROM #__users ";
        $query .= " WHERE id IN (".implode(',', $assignees).") AND id <> ".$row->userid;
        $recipients = PHPFrame::DB()->fetchObjectList($query);
        
        if (is_array($recipients) && count($recipients) > 0) {
            $failed_recipients = array();
            foreach ($recipients as $recipient) {
                if (PHPFrame_Utils_Filter::validate($recipient->email, 'email') === false ){
                    $failed_recipients[] = $recipient->email;
                    continue;
                } else {
                    $to_name = PHPFrame_User_Helper::fullname_format(
                                                         $recipient->firstname, 
                                                         $recipient->lastname
                                                     );
                    $new_mail->AddAddress($recipient->email, $to_name);
                    
                    // Send email
                    if ($new_mail->Send() !== true) {
                        $failed_recipients[] = $recipient->email;
                        continue;
                    }
                    
                    $new_mail->ClearAllRecipients();
                }
            }
            
            if (count($failed_recipients) > 0) {
                $msg = sprintf(_LANG_EMAIL_NOT_SENT, implode(',', $failed_recipients));
                throw new PHPFrame_Exception($msg);
            }
        } else {
            throw new PHPFrame_Exception(_LANG_ACTIVITYLOG_NO_RECIPIENTS);
        }
    }
}
