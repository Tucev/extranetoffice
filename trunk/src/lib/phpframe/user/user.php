<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	user
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * User Class
 *
 * @package		phpFrame
 * @subpackage 	user
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_User extends phpFrame_Database_Table {
	/**
	 * The user id
	 * 
	 * @var int
	 */
	var $id=null;
	/**
	 * The primary groupid
	 * 
	 * @var int
	 */
	var $groupid=null;
	/**
	 * The login username
	 * 
	 * @var string
	 */
	var $username=null;
	/**
	 * Primary email address
	 * 
	 * @var string
	 */
	var $email=null;
	/**
	 * The user's first name
	 * 
	 * @var string
	 */
	var $firstname=null;
	/**
	 * The user's last name
	 * 
	 * @var string
	 */
	var $lastname=null;
	/**
	 * A string containing the file name of the user's photo.
	 * 
	 * @var string
	 */
	var $photo=null;
	/**
	 * Boolean representing whether user wants to receive notifications via email.
	 * Default is "1" (true). Use values '0' and '1' as this columns is of data type ENUM.
	 * 
	 * @var string
	 */
	var $notifications=null;
	/**
	 * Boolean representing whether user wants to make their email address visible to other users.
	 * Default is "1" (true). Use values '0' and '1' as this columns is of data type ENUM.
	 * 
	 * @var string
	 */
	var $show_email=null;
	/**
	 * The date and time of creation
	 * 
	 * @var string
	 */
	var $created=null;
	/**
	 * The date and time of the last visit
	 * 
	 * @var string
	 */
	var $last_visit=null;
	/**
	 * Activation hash. This property will be used to store hash string to activate 
	 * accounts once we integrate the email activation feature.
	 * 
	 * @var string
	 */
	var $activation=null;
	/**
	 * User parameters
	 * 
	 * @var object
	 */
	var $params=null;
	/**
	 * This field is used to flag users as deleted by storing the delete date and time here.
	 * 
	 * @var string
	 */
	var $deleted=null;
	/**
	 * Full name. Combination of firstname + lastname
	 * 
	 * @var string
	 */
	var $name=null;
	/**
	 * Abbreviated full name
	 * 
	 * @var string
	 */
	var $name_abbr=null;
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct() {
		parent::__construct('#__users', 'id');
	}
	
	/**
	 * Load user row by id
	 * 
	 * This method overrides the inherited load method.
	 * 
	 * @access	public
	 * @param	int		$id 		The row id.
	 * @param	string	$exclude 	A list of key names to exclude from binding process separated by commas.
	 * @param	object	&$row 		The table row object use for binding. This parameter is passed by reference.
	 * 								This parameter is optional. If omitted the current instance is used ($this).
	 * @return	mixed	The loaded row object of FALSE on failure.
	 * @since 	1.0
	 */
	public function load($id, $exclude='password', &$row=null) {
		if (!parent::load($id, $exclude, $row)) {
			return false;
		}
		else {
			$this->name = $this->firstname.' '.$this->lastname;
			$this->name_abbr = phpFrame_User_Helper::fullname_format($this->firstname, $this->lastname);
			
			return $this;	
		}
	}
	
	/**
	 * Store user
	 * 
	 * This method overrides the inherited store method in order to encrypt the password before storing.
	 * 
	 * @access	public
	 * @param	object	&$row 	The table row object to store. This parameter is passed by reference.
	 * 							This parameter is optional. If omitted the current instance is used ($this).
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 * @since 	1.0
	 */
	public function store(&$row=null) {
		// Set row to $this if not passed in call.
		if (is_null($row)) {
			$row =& $this;
		}
		
		// Encrypt password for storage
		if (property_exists($row, 'password') && !is_null($row->password)) {
			$salt = phpFrame_Utils_Crypt::genRandomPassword(32);
			$crypt = phpFrame_Utils_Crypt::getCryptedPassword($row->password, $salt);
			$row->password = $crypt.':'.$salt;
		}
		
		// Invoke parent store() method to store row in db
		return parent::store($row);
	}
}
?>