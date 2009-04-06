<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * phpFrame Class
 * 
 * This class simply provides information about the installed phpFrame package.
 * 
 * @package		phpFrame
 * @since 		1.0
 */
class phpFrame {
	/**
	 * The phpFrame version
	 * 
	 * @var string
	 */
	const VERSION='1.0 Alpha';
	
	public static function getVersion() {
		return self::VERSION;
	}
}
?>