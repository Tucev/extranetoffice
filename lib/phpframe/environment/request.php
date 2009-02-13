<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

require_once 'lib/phpinputfilter/inputfilter.php';
		
class request {
	function init() {
		$inputfilter = new InputFilter();
		return $inputfilter->process($_REQUEST);
	}
	
	function getVar($name, $default='') {
		if (empty($GLOBALS['application']->request[$name])) {
			$GLOBALS['application']->request[$name] = $default;
		}
		return $GLOBALS['application']->request[$name];
	}
	
	function setVar($name, $value) {
		$inputfilter = new InputFilter();
		$GLOBALS['application']->request[$name] = $inputfilter->process($value);
	}
	
}
?>