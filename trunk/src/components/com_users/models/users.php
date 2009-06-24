<?php
/**
 * src/components/com_users/models/users.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * usersModelUsers Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_users
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class usersModelUsers extends PHPFrame_MVC_Model
{
    /**
     * Get users list
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
        $orderby="u.lastname", 
        $orderdir="ASC", 
        $limit=-1, 
        $limitstart=0, 
        $search=""
    ) {
        $rows = new PHPFrame_Database_RowCollection();
        
        $rows->select("*")
             ->from("#__users")
             ->where("deleted = '0000-00-00 00:00:00'", "OR", "deleted IS NULL");
        
        // Add search filtering
        if ($search) {
            $rows->where("lastname", "LIKE", ":search")
                 ->params(":search", "%".$search."%");
        }
        
        $rows->load();
        
        // Process row data before returning
        foreach ($rows as $row) {
            // Translate photo field to valid URL for frontend
            $photo = $row->photo;
            $photo_url = config::UPLOAD_DIR.'/users/';
            $photo_url .= !empty($photo) ? $photo : 'default.png';
            $row->set('photo', $photo_url);
            
            // Add url to detail page
            $detail_url = "index.php?component=com_users&action=get_user";
            $detail_url .= "&userid=".$row->id;
            $detail_url = PHPFrame_Utils_Rewrite::rewriteURL($detail_url);
            $row->detail_url = $detail_url;
        }
        
        return $rows;
    }
    
    /**
     * Get a single user's details
     * 
     * @param int $userid
     * 
     * @access public
     * @return PHPFrame_User
     * @since  1.0
     */
    public function getUser($userid) {
        // Instantiate user object
        $user = new PHPFrame_User();
        
        // Load user by id
        $user->load($userid);
        
        // Translate photo field to valid URL for frontend
        $photo = $user->photo;
        $photo_url = config::UPLOAD_DIR.'/users/';
        $photo_url .= !empty($photo) ? $photo : 'default.png';
        $user->set('photo', $photo_url);
        
        // Return user object
        return $user;
    }
    
    /**
     * Save user
     * 
     * @param array $post The post array
     * 
     * @access public
     * @return PHPFrame_User
     * @since  1.0
     */
    public function saveUser($post) {
        // Get reference to user object
        $user = PHPFrame::Session()->getUser();
        
        if ($post['id']) {
            $user->load($post['id'], 'password');
        }
        
        // Upload image if photo sent in request
        if (!empty($_FILES['photo']['name'])) {
            $dir = _ABS_PATH.DS."public".DS.config::UPLOAD_DIR.DS."users";
            $accept = 'image/jpeg,image/jpg,image/png,image/gif';
            $upload = PHPFrame_Utils_Filesystem::uploadFile('photo', $dir, $accept);
            if (!empty($upload['error'])) {
                $this->_error[] = $upload['error'];
                return false;
            }
            else {
                // resize image
                $image = new PHPFrame_Utils_Image();
                $img_path = $dir.DS.$upload['file_name'];
                $image->resize_image($img_path, $img_path, 80, 110);
                // Store file name in post array
                $post['photo'] = $upload['file_name'];
            }
        }
        
        // exlude password if not passed in request
        $exclude = '';
        if (empty($post['password'])) {
            $exclude = 'password';
        }
        
        // Bind the post data to the row array
        $user->bind($post, $exclude);
        // Store user in db
        $user->store();
        
        return $user;
    }
}
