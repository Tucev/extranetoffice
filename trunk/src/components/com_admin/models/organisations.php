<?php
/**
 * src/components/com_admin/models/organisations.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * adminModelOrganisations Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class adminModelOrganisations extends PHPFrame_MVC_Model
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
     * Get organisations
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
        // Create row collection object
        $rows = new PHPFrame_Database_RowCollection();
        $rows->select("o.*")
             ->from("#__organisations AS o");
             
        // Add search filtering
        if ($search) {
            $rows->where("o.name", "LIKE", ":search")
                 ->params(":search", "%".$search."%");
        }
        
        $rows->orderby($orderby, $orderdir)
             ->limit($limit, $limitstart);
        
        $rows->load();
        
        return $rows;
    }
    
    /**
     * Get organisation as database row
     * 
     * @param int $organisationid The id of the organisationid we want to get from the db
     * 
     * @access public
     * @return PHPFrame_Database_Row
     * @since  1.0
     */
    public function getRow($organisationid)
    {
        // Build SQL query to get row using IdObject
        $id_obj = new PHPFrame_Database_IdObject();
        $id_obj->select("o.*")
               ->from("#__organisations AS o")
               ->where("o.id", "=", ":organisationid")
               ->params(":organisationid", $organisationid);
        
        // Create instance of row
        $row = new PHPFrame_Database_Row('#__organisations');
        
        // Load row data using query and return
        return $row->load($id_obj);
    }
    
    
    /**
     * Save an organisation sent in the request.
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
        $row = new PHPFrame_Database_Row('#__organisations');
        
        // Bind the post data to the row array (exluding created and created_by)
        $row->bind($post);
        
        // Store row and return row object
        $row->store();
        
        return $row;
    }
    
    /**
     * Delete an organisation by id
     * 
     * @todo Before deleting the project we need to delete all its issue, files, ...
     * 
     * @param int $organisationid The id of the organisation we want to delete
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function deleteRow($organisationid)
    {
        // Instantiate table object
        $row = new PHPFrame_Database_Row('#__organisations');
        
        // Delete row from database
        $row->delete($organisationid);
    }
}
