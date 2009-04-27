<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	html
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Pagination Class
 * 
 * @package		phpFrame
 * @subpackage 	html
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_HTML_Pagination {
	/**
	 * The total number of rows
	 * 
	 * @var int
	 */
	var $total=null;
	/**
	 * The start position for rows in current page
	 * 
	 * @var int
	 */
	var $limitstart=null;
	/**
	 * The maximum number of rows per page
	 * 
	 * @var int
	 */
	var $limit=null;
	/**
	 * The total number of pages
	 * 
	 * @var int
	 */
	var $pages=null;
	/**
	 * The current page
	 * 
	 * @var int
	 */
	var $current_page=null;
	
	/**
	 * Constructor
	 * 
	 * @param	$total		The total number of rows
	 * @param	$limitstart	The start position for rows in current page
	 * @param	$limit		The maximum number of rows per page
	 * @return	void
	 */
	function __construct($total, $limitstart, $limit) {
		$this->total = $total;
		$this->limitstart = $limitstart;
		if ($limit == 'all') {
			$this->limit = $this->total;
		}
		else {
			$this->limit = $limit;
		}
		
		
		// Calculate number of pages
		$this->pages = (int) ceil($this->total/$this->limit);
		// Calculate current page
		$this->current_page = (int) (ceil($this->limitstart/$this->limit)+1);
	}
	
	/**
	 * getListFooter()
	 * 
	 * This method is invoked to print the pagination at the bottom of a data table.
	 * 
	 * @return	string	The HTML with the pagination footer
	 * @access	public
	 * @since	1.0
	 */
	public function getListFooter() {
		if ($this->pages > 1) {
			// Build limit select
			$html = '<div>';
			$html .= $this->getLimitSelect();
			$html .= '</div>';
			
			// Build page nav
			$html .= '<div>';
			$html .= $this->getPageNav();
			$html .= '</div>';
			
			// Display current page number
			$html .= '<div>';
			$html .= $this->getPageInfo();
			$html .= '</div>';
			
			return $html;
		}
		else {
			return false;
		}
	}
	
	/**
	 * getLimitSelect()
	 * 
	 * This method returns a string with HTML containing a select input to select the available pages.
	 * 
	 * @return string
	 * @access	public
	 * @since	1.0
	 */
	public function getLimitSelect() {
		$html .= '<form name="limitform" id="limitform" method="post">';
		$html .= 'Display Num: ';
		$html .= '<select name="limit" onchange="document.forms[\'limitform\'].submit();">';
		for ($i=25; $i<=100; $i+=25) {
			$html .= '<option value="'.$i.'"';
			if ($this->limit == $i) {
				$html .= ' selected';
			}
			$html .= '>'.$i.'</option>';
		}
		$html .= '<option value="all">-- All --</option>';
		$html .= '</select>';
		$html .= '</form>';
		
		return $html;
	}
	
	/**
	 * getPageNav()
	 * 
	 * This method returns a string with the HTML code containing the page navigation by page numbers.
	 * 
	 * Navigation elements are built into an unordered list of class "pageNav" (<ul class="pageNav">).
	 * 
	 * @return string
	 * @access	public
	 * @since	1.0
	 */
	public function getPageNav() {
		$href = 'index.php?component='.phpFrame_Environment_Request::getComponent();
		$href .= '&amp;view='.phpFrame_Environment_Request::getView();
		$href .= '&amp;layout='.phpFrame_Environment_Request::getVar('layout');
		$href .= '&amp;limit='.$this->limit;
		
		$html = '<ul>';
		// Start link
		$html .= '<li>';
		if ($this->current_page != 1) {
			$html .= '<a href="'.$href.'&amp;limitstart=0">Start</a>';
		}
		else {
			$html .= 'Start';
		}
		$html .= '</li>';
		// Prev link
		$html .= '<li>';
		if ($this->current_page != 1) {
			$html .= '<a href="'.$href.'&amp;limitstart='.(($this->current_page-2) * $this->limit).'">Prev</a>';
		}
		else {
			$html .= 'Prev';
		}
		$html .= '</li>';
		// Page numbers
		for ($j=0; $j<$this->pages; $j++) {
			$html .= '<li>';
			if ($this->current_page != ($j+1)) {
				$html .= '<a href="'.$href.'&amp;limitstart='.($this->limit * $j).'">'.($j+1).'</a>';	
			}
			else {
				$html .= ($j+1);
			}
			$html .= '</li>';
		}
		// Next link
		$html .= '<li>';
		if ($this->current_page != $this->pages) {
			$html .= '<a href="'.$href.'&amp;limitstart='.($this->current_page * $this->limit).'">Next</a>';	
		}
		else {
			$html .= 'Next';
		}
		// End link
		$html .= '<li>';
		if ($this->current_page != $this->pages) {
			$html .= '<a href="'.$href.'&amp;limitstart='.(($this->pages-1) * $this->limit).'">End</a>';	
		}
		else {
			$html .= 'End';
		}
		$html .= '</li>';
		$html .= '</ul>';
		
		return $html;
	}
	
	/**
	 * getPageInfo()
	 * 
	 * This method returns a string with the current page number and total number of pages.
	 * 
	 * ie: Page 1 of 4
	 * 
	 * @return 	string
	 * @access	public
	 * @since	1.0
	 */
	public function getPageInfo() {
		$html = 'Page '.$this->current_page.' of '.$this->pages;
		
		return $html;
	}
}
?>