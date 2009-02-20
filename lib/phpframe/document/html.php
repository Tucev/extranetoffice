<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	document
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * HTML Document Class
 * 
 * @package		phpFrame
 * @subpackage 	document
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class documentHTML extends document {
	/**
	 * Array of linked scripts
	 *
	 * @var		array
	 * @access   private
	 */
	var $_scripts_linked = array();

	/**
	 * Array of scripts placed in the header
	 *
	 * @var  array
	 * @access   private
	 */
	var $_scripts_inline = array();

	 /**
	 * Array of linked style sheets
	 *
	 * @var	 array
	 * @access  private
	 */
	var $_styles_linked = array();

	/**
	 * Array of included style declarations
	 *
	 * @var	 array
	 * @access  private
	 */
	var $_styles_inline = array();

	/**
	 * Array of meta tags
	 *
	 * @var	 array
	 * @access  private
	 */
	var $_metaTags = array();

	/**
	 * The rendering engine
	 *
	 * @var	 object
	 * @access  private
	 */
	var $_engine = null;

	/**
	 * The document type
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_type = null;
	
	/**
	 * Array for different tag types to be printed in <head></head>
	 *
	 * @var	 string
	 * @access  private
	 */
	var $_tagTypes = array("_scripts_linked","_styles_linked");
	
	/**
	 * Add linked scrip in document head
	 * 
	 * It takes both relative and absolute values
	 * 
	 * @param $src
	 * @param $type
	 * @return void
	 */
	function addScript($src, $type='text/javascript') {
		$this->_makeAbsolute($src);
		$this->_scripts_linked[] = '<script type="'.$type.'" src="'.$src.'"></script>';
	}
	
	/**
	 * Attach external stylesheet
	 * 
	 * @param $href
	 * @param $type
	 * @return void
	 */
	function addStyleSheet($href, $type='text/css') {
		$this->_makeAbsolute($href);
		$this->_styles_linked[] = '<link rel="stylesheet" href="'.$href.'" type="'.$type.'" />';
	}
	
	/**
	 * Print the head tags
	 * 
	 * @return void
	 */
	function printHead() {
		
		// For each tag type
		foreach($this->_tagTypes as $tagType){
			if (is_array($this->$tagType) && count($this->$tagType) > 0) {
				echo implode($this->_lineEnd, $this->$tagType).$this->_lineEnd;
			}
		}
	}
	
	/**
	 * Make a path absolute
	 * 
	 * @return string
	 */
	private function _makeAbsolute(&$path)
	{
		// Add the document base if a relative path
		if (substr($path, 0, 4) != 'http') {
			$path = $this->base.$path;
		}
	}
}
?>