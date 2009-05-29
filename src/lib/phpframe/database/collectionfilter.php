<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	database
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Collection Filter Class
 * 
 * @package		phpFrame
 * @subpackage 	database
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Database_CollectionFilter {
	/**
	 * Column to use for ordering
	 * 
	 * @var string
	 */
	private $_orderby=null;
	/**
	 * Order direction (either ASC or DESC)
	 * 
	 * @var string
	 */
	private $_orderdir=null;
	/**
	 * Number of rows per page
	 * 
	 * @var int
	 */
	private $_limit=null;
	/**
	 * Row number to start current page
	 * 
	 * @var int
	 */
	private $_limitstart=null;
	/**
	 * Search string
	 * 
	 * @var string
	 */
	private $_search=null;
	/**
	 * Total number of rows (in all pages)
	 * 
	 * @var int
	 */
	private $_total=null;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$_orderby		Column to use for ordering.
	 * @param	string	$_orderdir		Order direction (either ASC or DESC).
	 * @param	int		$_limit			Number of rows per page.
	 * @param	int		$_limitstart	Row number to start current page.
	 * @param	string	$_search		Search string.
	 * @return	void
	 */
	public function __construct($_orderby="", $_orderdir="ASC", $_limit=-1, $_limitstart=0, $_search="") {
		$this->_orderby = (string) $_orderby;
		$this->_orderdir = (string) $_orderdir;
		$this->_limit = (int) $_limit;
		$this->_limitstart = (int) $_limitstart;
		$this->_search = (string) $_search;
	}
	
	/**
	 * Set total number of records for the subset
	 * 
	 * @param	int		Total number of records in all pages.
	 * @return	void
	 */
	public function setTotal($int) {
		$this->_total = (int) $int;
	}
	
	/**
	 * Get search string
	 * 
	 * @return	string
	 */
	public function getSearchStr() {
		return $this->_search;
	}
	
	/**
	 * Get order by column name
	 * 
	 * @return	string
	 */
	public function getOrderBy() {
		return $this->_orderby;
	}
	
	/**
	 * Get order direction
	 * 
	 * @return	string	Either ASC or DESC
	 */
	public function getOrderDir() {
		return $this->_orderdir;
	}
	
	/**
	 * Get ORDER BY SQL statement
	 * 
	 * @return	string
	 */
	public function getOrderByStmt() {
		$stmt = "";
		
		if (is_string($this->_orderby) && $this->_orderby != "") {
			$stmt .= " ORDER BY ".$this->_orderby." ";
			$stmt .= ($this->_orderdir == "DESC") ? $this->_orderdir : "ASC";
		}
		
		return $stmt;
	}
	
	/**
	 * Get limit
	 * 
	 * @return	int
	 */
	public function getLimit() {
		return $this->_limit;
	}
	
	/**
	 * Get Limit start position
	 * 
	 * @return	int
	 */
	public function getLimitStart() {
		return $this->_limitstart;
	}
	
	/**
	 * Get LIMIT SQL statement
	 * 
	 * @return	string
	 */
	public function getLimitStmt() {
		$stmt = "";
		
		if ($this->_limit > 0) {
			$stmt .= " LIMIT ".$this->_limitstart.", ".$this->_limit;
		}
		
		return $stmt;
	}
	
	/**
	 * Get number of pages
	 * 
	 * @return int
	 */
	public function getPages() {
		if ($this->_limit > 0 && !is_null($this->_total)) {
			// Calculate number of pages
			return (int) ceil($this->_total/$this->_limit);
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Get current page number
	 * 
	 * @return int
	 */
	public function getCurrentPage() {
		// Calculate current page
		if ($this->_limit > 0) {
			return (int) (ceil($this->_limitstart/$this->_limit)+1);
		}
		else {
			return 0;
		}
	}
}
