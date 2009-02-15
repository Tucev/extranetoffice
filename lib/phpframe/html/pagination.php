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

class pagination {
	var $total=null;
	var $limitstart=null;
	var $limit=null;
	var $pages=null;
	var $current_page=null;
	
	function __construct($total, $limitstart, $limit) {
		$this->total = $total;
		$this->limitstart = $limitstart;
		$this->limit = $limit;
		
		// Calculate number of pages
		$this->pages = (int) ceil($this->total/$this->limit);
		// Calculate current page
		$this->current_page = (int) (ceil($this->limitstart/$this->limit)+1);
	}
	
	function getListFooter() {
		echo 'Display Num: ';
		echo '<form name="limitform" id="limitform" method="post">';
		echo '<select name="limit" onchange="document.forms[\'limitform\'].submit();">';
		for ($i=25; $i<=100; $i+=25) {
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
		echo '<option value="all">-- All --</option>';
		echo '</select>';
		echo '</form>';
		
		echo '<pre>'; var_dump($this); echo '</pre>';
		
		
	}
}
?>