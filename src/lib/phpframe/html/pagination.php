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
	 * The list filter object for which to provide pagination
	 * 
	 * @var	object	Object of type phpFrame_Database_Listfilter
	 */
	private $_list_filter=null;
	
	/**
	 * Constructor
	 * 
	 * @param	object	$list_filter	Object of type phpFrame_Database_Listfilter
	 * @return	void
	 */
	public function __construct(phpFrame_Database_Listfilter $list_filter) {
		$this->_list_filter = $list_filter;
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
		if ($this->_list_filter->getPages() > 1) {
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
	 * @return	string
	 * @access	public
	 * @since	1.0
	 */
	public function getLimitSelect() {
		$html .= '<form name="limitform" id="limitform" method="post">';
		$html .= 'Display Num: ';
		$html .= '<select name="limit" onchange="document.forms[\'limitform\'].submit();">';
		for ($i=25; $i<=100; $i+=25) {
			$html .= '<option value="'.$i.'"';
			if ($this->_list_filter->getLimit() == $i) {
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
		$href = 'index.php?component='.phpFrame_Environment_Request::getComponentName();
		$href .= '&amp;view='.phpFrame_Environment_Request::getViewName();
		$href .= '&amp;layout='.phpFrame_Environment_Request::getLayout();
		$href .= '&amp;limit='.$this->_list_filter->getLimit();
		
		$html = '<ul>';
		// Start link
		$html .= '<li>';
		if ($this->_list_filter->getCurrentPage() != 1) {
			$html .= '<a href="'.$href.'&amp;limitstart=0">Start</a>';
		}
		else {
			$html .= 'Start';
		}
		$html .= '</li>';
		// Prev link
		$html .= '<li>';
		if ($this->_list_filter->getCurrentPage() != 1) {
			$html .= '<a href="'.$href.'&amp;limitstart='.(($this->_list_filter->getCurrentPage()-2) * $this->_list_filter->getLimit()).'">Prev</a>';
		}
		else {
			$html .= 'Prev';
		}
		$html .= '</li>';
		// Page numbers
		for ($j=0; $j<$this->_list_filter->getPages(); $j++) {
			$html .= '<li>';
			if ($this->_list_filter->getCurrentPage() != ($j+1)) {
				$html .= '<a href="'.$href.'&amp;limitstart='.($this->_list_filter->getLimit() * $j).'">'.($j+1).'</a>';	
			}
			else {
				$html .= ($j+1);
			}
			$html .= '</li>';
		}
		// Next link
		$html .= '<li>';
		if ($this->_list_filter->getCurrentPage() != $this->_list_filter->getPages()) {
			$html .= '<a href="'.$href.'&amp;limitstart='.($this->_list_filter->getCurrentPage() * $this->_list_filter->getLimit()).'">Next</a>';	
		}
		else {
			$html .= 'Next';
		}
		// End link
		$html .= '<li>';
		if ($this->_list_filter->getCurrentPage() != $this->_list_filter->getPages()) {
			$html .= '<a href="'.$href.'&amp;limitstart='.(($this->_list_filter->getPages()-1) * $this->_list_filter->getLimit()).'">End</a>';	
		}
		else {
			$html .= 'End';
		}
		$html .= '</li>';
		$html .= '</ul>';
		
		return $html;
	}
	
	/**
	 * Get page info
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
		$str = 'Page '.$this->_list_filter->getCurrentPage();
		$str .= ' of '.$this->_list_filter->getPages();
		return $str;
	}
}
