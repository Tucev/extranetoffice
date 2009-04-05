<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Pathway Class
 * 
 * @package		phpFrame
 * @subpackage 	application
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Application_Pathway extends phpFrame_Base_Singleton {
	/**
	 * Array containing the pathway item objects
	 * 
	 * @var array
	 */
	var $pathway=null;
	/**
	 * Items counter
	 * 
	 * @var int
	 */
	var $count;
	
	/**
	 * Constructor
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function __construct() {
		$this->pathway = array();
		
		$item = new phpFrame_Base_StdObject();
		$item->set('title', 'Home');
		$item->set('url', 'index.php');
		
		$this->pathway[] = $item;
	}
	
	/**
	 * Add a pathway item
	 * 
	 * @param	string	$title The pathway item title.
	 * @param 	string	$url The pathway item URL.
	 * @return 	void
	 * @since	1.0
	 */
	function addItem($title, $url) {
		$item = new phpFrame_Base_StdObject();
		$item->set('title', $title);
		$item->set('url', $url);
		
		$this->pathway[] = $item;
	}
	
	/**
	 * Set the pathway array
	 * 
	 * @param	array 	$pathway An array of pathway item objects.
	 * @return 	array
	 * @since	1.0
	 */
	function setPathway($pathway) {
		$oldPathway = $this->pathway;
		$pathway = (array) $pathway;
        
        // Set the new pathway.
        $this->_pathway = array_values($pathway);
        
        return array_values($oldPathway);
	}
	
	/**
	 * Echo pathway as HTML
	 * 
	 * @return 	void
	 * @since	1.0
	 */
	function display() {
		echo '<div class="pathway">';
		for ($i=0; $i<count($this->pathway); $i++) {
			if ($i>0) {
				echo ' &gt;&gt; ';
			}
			echo '<span class="pathway_item">';
			if (!empty($this->pathway[$i]->url)) {
				echo '<a href="'.$this->pathway[$i]->url.'">'.$this->pathway[$i]->title.'</a>';
			}
			else {
				echo $this->pathway[$i]->title;
			}
			echo '</span>';
		}
		echo '</div>';
	}
}
?>