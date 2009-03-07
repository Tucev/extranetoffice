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
	 * Build a date picker using jQuery UI Calendar and display it
	 * 
	 * This method will generate two input tags, one is shown to the user and it triggers 
	 * the date picker, and the other one holding the date value in MySQL date format to 
	 * be used for storing.
	 * 
	 * @todo	add jQuery tooltip: display format hint in tooltip
	 * @param	string	$name		The name attribute for the input tag. 
	 * @param	string	$id			The id of the input tag
	 * @param	string	$selected	The selected value if any. In YYYY-MM-DD.
	 * @param	string	$format		Format in which to present the date to the user. Possible values 'dd/mm/yy', 'mm/dd/yy', 'yy/mm/dd'. 
	 * 								This doesn't affect the hidden input value with the MySQL date.
	 * @param 	array	$attribs	An array containing attributes for the input tag
	 * @param	bool	$show_format_hint	Show/hide date format hint.
	 * @return	void
	 */
	static function calendar($name, $id='', $selected='', $format='dd/mm/yy', $attribs=array(), $show_format_hint=false) {
		//set $id to $name if empty
		if (empty($id)) {
			$id = $name;
		}
		
		// Convert attributes array to string
		$attribs_str = '';
		if (is_array($attribs) && count($attribs) > 0) {
			foreach ($attribs as $key=>$value) {
				$attribs_str .= $key.'="'.$value.'" ';
			}
		}
		
		// Format selected value using format parameter
		$formatted_date = '';
		$formatted_date_array = explode('-', $selected);
		$format_array = explode('/', $format);
		foreach ($format_array as $format_item) {
			switch ($format_item) {
				case 'yy' :
					$key = 0;
					break;
				case 'mm' :
					$key = 1;
					break;
				case 'dd' :
					$key = 2;
					break;
			}
			if (!empty($formatted_date)) {
				$formatted_date .= '/';
			} 
			$formatted_date .= $formatted_date_array[$key];
		}
			
		//invoking datepicker via jquery
		?>
		<script type="text/javascript">
		$(function(){
			$('#<?php echo $id; ?>_datePicker').datepicker({
				inline: true,
				<?php if ($show_format_hint) : ?>appendText: '(dd/mm/yyyy)',<?php endif; ?>
				dateFormat: '<?php echo $format; ?>',
				altField: '#<?php echo $id; ?>',
				altFormat: 'yy-mm-dd'
			});
		});	
		</script>
		<input id="<?php echo $id; ?>_datePicker" type="text" name="<?php echo $name; ?>_datePicker" <?php echo $attribs_str; ?> value="<?php echo $formatted_date; ?>" />
		<input id="<?php echo $id; ?>" type="hidden" name="<?php echo $name; ?>" value="<?php echo $selected; ?>" />
		<?php	
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
		?>
		
		<input autocomplete="off" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="text" <?php echo $attribs; ?> />

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
     * Use in conjuction with crypt::checkToken
     * 
	 * @return	string
	 * @return 	void
	 * @since 	1.0
	 */
	static function formToken() {
		return '<input type="hidden" name="'.crypt::getToken().'" value="1" />';
	}
	
	/**
	 * Build an html button tag and echo it.
	 * 
	 * @param string $type The button type. Possible values are 'button', 'submit', 'reset'
	 * @param string $label A string to use as the button's label.
	 * @param string $onclick A string to be printed in the onclick attribute of the button tag.
	 * @return void
	 */
	static function button($type='button', $label='', $onclick='') {
		?>
		<button type="<?php echo $type; ?>" onclick="<?php echo $onclick; ?>"><?php echo text::_( $label ); ?></button> 
		<?php
	}
	
	/**
	 * Build an html 'back' button tag and echo it.
	 * 
	 * @return void
	 */
	static function buttonBack() {
		?>
		<button type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button> 	
		<?php
	}
	
	/**
	 * Build an html 'save' button tag and echo it.
	 * 
	 * This button will have an onclick attribute of 'submitbutton('save'); return false;'.
	 * 
	 * @return void
	 */
	static function buttonSave() {
		?>
		<button type="button" onclick="submitbutton('save'); return false;"><?php echo text::_( _LANG_SAVE ); ?></button>
		<?php
	}
	
	/**
	 * Build an html 'apply' button tag and echo it.
	 * 
	 * This button will have an onclick attribute of 'submitbutton('apply'); return false;'.
	 * 
	 * @return void
	 */
	static function buttonApply() {
		?>
		<button type="button" onclick="submitbutton('apply'); return false;"><?php echo text::_( _LANG_APPLY ); ?></button>
		<?php
	}
	
	/**
	 * Redirects to previous page
	 * 
	 * @return	void
	 */
	static function historyBack() {
		?>
		
		<script type="text/javascript">
			window.history.back();
		</script>

		<?php  
	}
	
	/**
	 * Outputs message in alert box
	 *
	 * @param	str $msg
	 * @return	void
	 */
	static function alert($msg) {
		?>
		
		<script type="text/javascript">
			alert('<?php echo $msg; ?>');
		</script>

		<?php  
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