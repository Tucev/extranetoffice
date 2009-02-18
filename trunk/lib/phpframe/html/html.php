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

class html {
	
	function selectOption($value, $label) {
		$html = '<option value="'.$value.'">';
		$html .= $label;
		$html .= '</option>';
		
		return $html;
	}
	
	function selectGenericlist($options, $name, $attribs, $key='value', $text='text', $selected=NULL, $idtag=false, $translate=false) {
		$html = '<select name="'.$name.'" '.$attribs.'>';
		foreach ($options as $option) {
			$html .= $option;
		}
		$html .= '</select>';
		
		echo $html;
	}
	
	/**
	 * Displays a hidden token field to reduce the risk of CSRF exploits.
	 * 
     * Use in conjuction with JRequest::checkToken
     * 
	 * @return string
	 */
	function formToken() {
		return '<input type="hidden" name="'.crypt::getToken().'" value="1" />';
	}
	
	/**
	 * Class loader method
	 * 
	 * @param $str
	 * @return void
	 */
	function _($str) {
		
		$array = explode('.', $str);
		$function_name = $array[0].ucfirst($array[1]);
		
		if (is_callable( array( 'html', $function_name) )) {
			$args = func_get_args();
			array_shift( $args );
			return call_user_func_array( array( 'html', $function_name ), $args );
		}
		else {
			error::raise('', 'warning', 'html::'.$function_name.' not supported.' );
			return false;
		}
		
	}
}
?>