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

/**
 * HTML Class
 * 
 * @package		phpFrame
 * @subpackage 	html
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class html {
	/**
	 * Build an html option tag
	 * 
	 * @param	string	$value The option value
	 * @param	string	$label The option label
	 * @return 	string
	 * @since 	1.0
	 */
	static function selectOption($value, $label) {
		$html = '<option value="'.$value.'">';
		$html .= $label;
		$html .= '</option>';
		
		return $html;
	}
	
	/**
	 * Build a generic select tag.
	 * 
	 * @todo	Have to make selected item actually be selected...
	 * @param	array	$options An array of option tags
	 * @param	string	$name
	 * @param	string	$attribs
	 * @param 	string	$selected
	 * @return	string
	 * @since 	1.0
	 */
	static function selectGenericlist($options, $name, $attribs, $selected=NULL) {
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
	 * @return	string
	 * @since 	1.0
	 */
	static function formToken() {
		return '<input type="hidden" name="'.crypt::getToken().'" value="1" />';
	}
	
	/**
	 * Loader method.
	 * 
	 * Use this method to create html elements by using keywords to invoke the methods.
	 * 
	 * For example: 
	 * 
	 * <code>
	 * $options[] = html::_('select.option', $row->id, $row->name );
	 * $output = html::_('select.genericlist', $options, 'projectid', $attribs, $selected);
	 * </code>
	 * 
	 * @param	string	$str
	 * @return 	void
	 * @since 	1.0
	 */
	static function _($str) {
		
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