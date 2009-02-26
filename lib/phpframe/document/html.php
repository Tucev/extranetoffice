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
	 * @access	private
	 */
	var $_scripts_linked = array();

	/**
	 * Array of scripts placed in the header
	 *
	 * @var		array
	 * @access	private
	 */
	var $_scripts_inline = array();

	 /**
	 * Array of linked style sheets
	 *
	 * @var		array
	 * @access	private
	 */
	var $_styles_linked = array();

	/**
	 * Array of included style declarations
	 *
	 * @var		array
	 * @access	private
	 */
	var $_styles_inline = array();

	/**
	 * Array of meta tags
	 *
	 * @var		array
	 * @access	private
	 */
	var $_metaTags = array();

	/**
	 * The rendering engine
	 *
	 * @var		object
	 * @access	private
	 */
	var $_engine = null;

	/**
	 * The document type
	 *
	 * @var		string
	 * @access	private
	 */
	var $_type = null;
	
	/**
	 * Array for different tag types to be printed in <head></head>
	 *
	 * @var		string
	 * @access	private
	 */
	var $_tagTypes = array("_metaTags","_scripts_linked","_styles_linked");
	
	/**
	 * Constructor
	 *
	 * @return	void
	 * @access	public
	 * @since	0.1 
	 */
	function __construct(){
		parent::__construct();
		$this->_type = $this->_mime."; charset=".$this->_charset;
	}
	
	/**
	 * Add meta tag
	 * 
	 * @param	string	$name
	 * @param	string	$content
	 * @return	void
	 * @since 1.0
	 */
	function addMetaTag($name, $content) {
		$this->_metaTags[] = '<meta name="'.$name.'" content="'.$content.'" />';
	}
	
	/**
	 * Add linked scrip in document head
	 * 
	 * It takes both relative and absolute values
	 * 
	 * @param	string	$src
	 * @param	string	$type
	 * @return void
	 * @since 1.0
	 */
	function addScript($src, $type='text/javascript') {
		$this->_makeAbsolute($src);
		$this->_scripts_linked[] = '<script type="'.$type.'" src="'.$src.'"></script>';
	}
	
	/**
	 * Attach external stylesheet
	 * 
	 * @param	string	$href
	 * @param	string	$type
	 * @return void
	 * @since 1.0
	 */
	function addStyleSheet($href, $type='text/css') {
		$this->_makeAbsolute($href);
		$this->_styles_linked[] = '<link rel="stylesheet" href="'.$href.'" type="'.$type.'" />';
	}
	
	/**
	 * Print the head tags
	 * 
	 * @return	void
	 * @since	1.0
	 */
	function printHead() {
		
		// add meta tags
		$this->_metaTags[] = '<meta name="generator" content="Extranet Office" />';
		$this->_metaTags[] = '<meta name="Content-Type" content="'.$this->_type.'" />';
		
		// print base url
		echo '<base href="'.$this->base.'" />'.$this->_lineEnd;
		
		// For each tag type
		foreach($this->_tagTypes as $tagType){
			if (is_array($this->$tagType) && count($this->$tagType) > 0) {
				echo implode($this->_lineEnd, $this->$tagType).$this->_lineEnd;
			}
		}
	}
	
	/**
	 * Make path absolute
	 * 
	 * @param	string	$path
	 * @return	void
	 * @since	1.0
	 */
	private function _makeAbsolute(&$path) {
		// Add the document base if a relative path
		if (substr($path, 0, 4) != 'http') {
			$path = $this->base.$path;
		}
	}
}
?>