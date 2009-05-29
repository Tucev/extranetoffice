<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	exception
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Database Exception Class
 * 
 * @package		phpFrame_lib
 * @subpackage 	exception
 * @since 		1.0
 */
class phpFrame_Exception_Database extends phpFrame_Exception {
	/**
	 * MySQL error message
	 * 
	 * @access	private
	 * @var		string
	 */
	private $_mysql_error=null;
	/**
	 * MySQL error number
	 * 
	 * @access	private
	 * @var		int
	 */
	private $_mysql_errno=null;
	
	/**
	 * Constructor
	 * 
	 * @access	public
	 * @param	string	$message	The error message.
	 * @param	int		$code		The error code.
	 * @param	string	$query
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($message=null, $query="", $code=self::E_USER_ERROR) {
		$this->_mysql_error = mysql_error();
		$this->_mysql_errno = mysql_errno();
		
		$verbose = "MySQL Error Number: ".$this->_mysql_errno."\n";
		$verbose .= "MySQL Server said: ".$this->_mysql_error;
		
		parent::__construct($message, $code, $verbose);
	}
}
