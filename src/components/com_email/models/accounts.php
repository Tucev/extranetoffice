<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_email
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * emailModelAccounts Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_email
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		phpFrame_Application_Model
 */
class emailModelAccounts extends phpFrame_Application_Model {
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
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();	
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
		$row =& phpFrame_Base_Singleton::getInstance("emailTableAccounts");
		
		if (!empty($post['id'])) {
			$row->load($post['id']);
		}
		
		// Check whether this is the first account to be set up so that we make it default
		$accounts = $this->getAccounts($this->_user->id);
		if (!is_array($accounts) || count($accounts) == 0) {
			$row->default = '1';	
		}
		
		if (!$row->bind($post)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		$row->userid = $this->_user->id;
		
		if (!$row->check()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		
		return $row;
	}
	
	public function deleteAccount($accountid) {
		require_once COMPONENT_PATH.DS."tables".DS."email_accounts.table.php";		
		$row =& phpFrame_Base_Singleton::getInstance("emailTableAccounts");
		
		if (!$row->delete($accountid)) {
			$this->_error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	/**
	 * Make an email account the default account for the current user
	 * 
	 * @param	int		$accountid
	 * @return	boolean
	 */
	public function makeDefault($accountid) {
		// First we make sure that all accounts are set not to be default (we do this to avoid duplicate default accounts)
		$query = "UPDATE `#__email_accounts` SET `default` = '0' WHERE `userid` = ".$this->_user->id;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->_error[] = $this->_db->getLastError();
			return false;
		}
		
		// Make the selected account the default account
		$query = "UPDATE `#__email_accounts` SET `default` = '1' WHERE `userid` = ".$this->_user->id." AND `id` = ".$accountid;
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->_error[] = $this->_db->getLastError();
			return false;
		}
		
		return true;
	}
	
}
?>