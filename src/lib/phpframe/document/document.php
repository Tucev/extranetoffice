<?php
/**
 * @version		$Id$
 * @package		phpFrame_lib
 * @subpackage 	document
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Document Class
 * 
 * @package		phpFrame_lib
 * @subpackage 	document
 * @since 		1.0
 */
class phpFrame_Document extends phpFrame_Base_Singleton {
	/**
	 * Document title
	 *
	 * @var	 	string
	 * @access  public
	 */
	var $title = '';

	/**
	 * Document description
	 *
	 * @var	 	string
	 * @access  public
	 */
	var $description = '';

	/**
	 * Document URI
	 *
	 * @var	 	string
	 * @access  public
	 */
	var $uri = '';

	/**
	 * Document base URL
	 *
	 * @var	 	string
	 * @access  public
	 */
	var $base = '';

	 /**
	 * Contains the document language setting
	 *
	 * @var	 	string
	 * @access  public
	 */
	var $language = 'en-gb';

	/**
	 * Document modified date
	 *
	 * @var		string
	 * @access	private
	 */
	var $_mdate = '';

	/**
	 * Tab string
	 *
	 * @var		string
	 * @access	private
	 */
	var $_tab = "\11";

	/**
	 * Contains the line end string
	 *
	 * @var		string
	 * @access	private
	 */
	var $_lineEnd = "\12";

	/**
	 * Contains the character encoding string
	 *
	 * @var	 	string
	 * @access  private
	 */
	var $_charset = 'utf-8';

	/**
	 * Document mime type
	 *
	 * @var		string
	 * @access	private
	 */
	var $_mime = 'text/html';

	/**
	 * Document namespace
	 *
	 * @var		string
	 * @access	private
	 */
	var $_namespace = '';
	
	/**
	 * Constructor
	 * 
	 * @return	void
	 * @access	public
	 * @since	1.0
	 */
	public function __construct() {
		$uri = phpFrame::getURI();
		$this->base = $uri->getBase();
		$this->uri = $uri->__toString();
	}
}
?>