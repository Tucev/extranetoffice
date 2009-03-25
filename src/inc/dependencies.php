<?php
/**
 * @version		$Id: dependencies.php 25 2009-01-28 14:31:38Z luis.montero $
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class dependencies {
	/**
	 * A boolean indicating whether dependencies are met ot not
	 *
	 * @var bool
	 */
	var $status=null;
	/**
	 * A string containing the installed php version
	 *
	 * @var string
	 */
	var $php_version=null;
	/**
	 * An array containing asoc arrays with info about php extensions to test.
	 *
	 * @var array
	 */
	var $php_extensions=null;
	/**
	 * An array to hold the dependencies check results
	 *
	 * @var array
	 */
	var $result=null;
	
	function __construct() {
		// set php functions to test
		$this->php_extensions[] = array('function'=>'mysql_connect', 
									   'package'=>'mysql', 
									   'required' => 'yes', 
									   'description'=>'These functions allow you to access MySQL database servers. More information about MySQL can be found at http://www.mysql.com/.', 
									   'since'=> '4',  
									   'man'=>'http://www.php.net/manual/en/book.mysql.php');
		
		$this->php_extensions[] = array('function'=>'filter_list', 
									   'package'=>'filter', 
									   'required' => 'yes', 
									   'description'=>'This extension serves to validate and filter data coming from some insecure source, such as user input.', 
									   'since'=> '5.2.0',  
									   'man'=>'http://www.php.net/manual/en/book.filter.php');
		
		$this->php_extensions[] = array('function'=>'gd_info', 
									   'package'=>'gd', 
									   'required' => 'yes', 
									   'description'=>'This extension allows to manipulate images.', 
									   'since'=> '4.3.0',  
									   'man'=>'http://www.php.net/manual/en/book.image.php');
		
		$this->php_extensions[] = array('function'=>'imap_open', 
									   'package'=>'imap', 
									   'required' => 'yes', 
									   'description'=>'These functions enable you to operate with the IMAP protocol, as well as the NNTP, POP3 and local mailbox access methods.',
									   'since' => '4',  
									   'man'=>'http://www.php.net/manual/en/book.imap.php');
		
		$this->checkDependencies();
		
		if ($this->status !== false) {
			$this->status = true;
		}
	}
	
	function checkDependencies() {
		$this->checkPHPVersion();
		$this->checkPHPExtensions($this->php_extensions);
		$this->checkMySQLVersion();
	}
	
	function checkPHPVersion() {
		$this->php_version = phpversion();
	}
	
	function checkPHPExtensions($array) {
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $item) {
				if (function_exists($item['function'])) {
					$this->result['php_extensions'][] = array($item['package'], true);
					
				} 
				else {
					$this->result['php_extensions'][] = array($item['package'], false);
					if ($item['required'] == 'yes') {
						$this->status = false;
					}
				}
			}
		}
	}
	
	function checkMySQLVersion() {
		
	}
}
?>