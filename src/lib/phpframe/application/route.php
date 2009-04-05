<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	application
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

class phpFrame_Application_Route {
	static function _($str, $javascript_safe=false) {
		// For now the only thing we do is adding the tmpl var to the query string if needed
		$tmpl = phpFrame_Environment_Request::getVar('tmpl', '');
		if (!empty($tmpl) && strpos($str, '&tmpl=') === false) {
			$str .= '&tmpl='.$tmpl;
		}
		
		return $str;
	}
}
?>