<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	registry
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Abstract Registry Class
 * 
 * @package		phpFrame
 * @subpackage 	registry
 * @since 		1.0
 */
abstract class phpFrame_Registry {
	
	abstract protected function __construct();
	
	abstract public static function getInstance();
	
	abstract public function get($key, $default_value=null);
	
	abstract public function set($key, $value);
}
