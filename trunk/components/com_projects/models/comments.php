<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelComments Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelComments extends model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	function getComments($projectid, $type, $itemid) {
		$query = "SELECT c.*, u.username AS created_by_name";
		$query .= " FROM #__comments AS c ";
		$query .= " JOIN #__users u ON u.id = c.userid ";
		$query .= " WHERE c.projectid = ".$projectid." AND c.type = '".$type."' AND c.itemid = ".$itemid;
		//echo $query; exit;	  
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		return $rows;
	}
	
	function saveComment($projectid) {
		require_once COMPONENT_PATH.DS."tables".DS."comments.table.php";
		$row =& phpFrame::getInstance("projectsTableComments");
		
		$post = request::get('post');
		$row->bind($post);
		
		$row->userid = $this->user->id;
		$row->created = date("Y-m-d H:i:s");
		
		if (!$row->check()) {
			$this->error =& $row->error;
			return false;
		}
	
		if (!$row->store()) {
			$this->error =& $row->error;
			return false;
		}
		
		return $row;
	}
	
	function deleteComment($projectid, $commentid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		
		// Instantiate table object
		$row =& phpFrame::getInstance("projectsTableComments");
		
		// Delete row from database
		if (!$row->delete($commentid)) {
			$this->error =& $row->error;
			return false;
		}
		else {
			return true;
		}
	}
	
	function itemid2title($itemid, $type) {
		switch ($type) {
			case 'issues' :
				$query = "SELECT title FROM #__issues WHERE id = ".$itemid;
				break;
			case 'messages' :
				$query = "SELECT subject FROM #__messages WHERE id = ".$itemid;
				break;
			case 'files' :
				$query = "SELECT title FROM #__files WHERE id = ".$itemid;
				break;
		}
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	function getTotalComments($itemid, $type) {
		$query = "SELECT COUNT(id) FROM #__comments ";
		$query .= " WHERE itemid = ".$itemid." AND type = '".$type."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
}
?>