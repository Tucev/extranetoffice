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
	
	function getAccounts($userid=0, $accountid=0, $default=false) {
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
	
}
?>