<?php
class phpFrame_Database_Listfilter {
	private $_orderby=null;
	private $_orderdir=null;
	private $_limit=null;
	private $_limitstart=null;
	private $_search=null;
	private $_total=null;
	
	/**
	 * Constructor
	 * 
	 * @param	string	$_orderby
	 * @param	string	$_orderdir
	 * @param	int		$_limit
	 * @param	int		$_limitstart
	 * @param	string	$_search
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
	
	public function getPages() {
		if ($this->_limit > 0 && !is_null($this->_total)) {
			// Calculate number of pages
			return (int) ceil($this->_total/$this->_limit);
		}
		else {
			return 0;
		}
	}
	
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
