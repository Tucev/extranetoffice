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

class route {
	static function _($str, $javascript_safe=false) {
		// For now the only thing we do is adding the tmpl var to the query string if needed
		$tmpl = request::getVar('tmpl', '');
		if (!empty($tmpl) && strpos($str, '&tmpl=') === false) {
			$str .= '&tmpl='.$tmpl;
		}
		
		return $str;
	}
}
?>