<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage	html
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
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
class pagination {
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
	 */
	function getListFooter() {
		// Build limit select
		$html = '<div>';
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
		$html .= '</div>';
		
		// Build page nav
		$href = 'index.php?option='.request::getVar('option');
		$href .= '&amp;view='.request::getVar('view');
		$href .= '&amp;layout='.request::getVar('layout');
		$href .= '&amp;limit='.$this->limit;
		
		$html .= '<div>';
		$html .= '<ul>';
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
		$html .= '</div>';
		
		// Display current page number
		$html .= '<div>';
		$html .= 'Page '.$this->current_page.' of '.$this->pages;
		$html .= '</div>';
		
		return $html;
	}
}
?>