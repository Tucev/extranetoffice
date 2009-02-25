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
	 * Build an select option object
	 * 
	 * @param	string	$value The option value
	 * @param	string	$label The option label
	 * @return 	object
	 * @since 	1.0
	 */
	static function selectOption($value, $label) {
		$option = new standardObject();
		$option->value = $value;
		$option->label = $label;
		
		return $option;
	}
	
	/**
	 * Build a generic select tag.
	 * 
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
			$html .= '<option value="'.$option->value.'"';
			if ($option->value == $selected) {
				$html .= ' selected';
			}
			$html .= '>';
			$html .= $option->label;
			$html .= '</option>';
		}
		$html .= '</select>';
		
		echo $html;
	}
	
	/**
	 * Build a date picker and display it
	 * 
	 * @param	string	$selected
	 * @param	string	$name
	 * @param	string	$id
	 * @param	string	$format
	 * @param 	array	$attribs
	 * @return unknown_type
	 */
	static function calendar($selected, $name, $id='', $format='%Y-%m-%d', $attribs='') {
		$html = '<input type="text" name="'.$name.'" />';
		echo $html;
	}
	
	/**
	 * Function to build input with autocomplete and display it
	 * 
	 * @static
	 * @todo	This method needs to be refactored using jQuery instead of mootools.
	 * @param	string	$form_name	The name of the form where this input tag will appear
	 * @param 	string	$field_name	The name attribute fot the input tag
	 * @param	string	$attribs 	A string containing attributes for the input tag
	 * @param	array	$tokens		An array with the key/value pairs used to build the list of options
	 * @return 	void
	 * @since	1.0
	 */
	static function autocompleter($form_name, $field_name, $attribs, $tokens) {
		// Attach style sheets and scripts
		$document =& factory::getDocument('html');
		$document->addScript('lib/jquery/jquery-1.3.1.min.js');
		?>
		
		<input class="inputbox" autocomplete="off" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" <?php echo $attribs; ?> />

		<script type="text/javascript">
		window.addEvent('domready', function(){
			var el = document.<?php echo $form_name; ?>.<?php echo $field_name; ?>;
			
			var tokens = [
				<?php for($i=0; $i<count($tokens); $i++) : ?>
				<?php if($i>0) { echo ', '; } ?>
				['<?php echo $tokens[$i][0]; ?>','<?php echo $tokens[$i][1]; ?>']
				<?php endfor; ?>
			];
			
			var completer1 = new Autocompleter.Local(el, tokens, {
				'delay': 100,
				'filterTokens': function() {
					var regex = new RegExp('^' + this.queryValue.escapeRegExp(), 'i');
					return this.tokens.filter(function(token) {
						return (regex.test(token[0]) || regex.test(token[1]));
					});
				},
				'injectChoice': function(choice) {
					var el = new Element('li', {'class': 'autocomplete'})
					.setHTML(this.markQueryValue(choice[0]));
					el.inputValue = choice[0];
					this.addChoiceEvents(el).injectInside(this.choices);
				}
			});
			
		});
		</script>
		
		<?php
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