<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailModelAccounts Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class emailModelAccounts extends model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	public function getAccounts($userid=0, $accountid=0, $default=false) {
		if (!empty($userid)) {
			$query = "SELECT * ";
			$query .= " FROM #__email_accounts ";
			$query .= " WHERE userid = ".$userid;
			if (!empty($accountid)) {
				$query .= " AND id = ".$accountid;
			}
			elseif ($default == true) {
				$query .= " AND `default` = '1'";
			}
			$this->db->setQuery($query);
			return $this->db->loadObjectList();	
		}
		else {
			return false;
		}
	}
	
	/**
	 * Save email account
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveAccount($post) {
		require_once COMPONENT_PATH.DS."tables".DS."email_accounts.table.php";		
		$row =& phpFrame::getInstance("emailTableAccounts");
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		$row->userid = $this->user->id;
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
}
?>