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
 * This class provides a number of static methods to be used for generating useful HTML elements and Javascript.
 * This class is mostly used in the views tmpl layer for quickly building buttons, calendars, autocomleters, and so on.
 * 
 * All methods in this class are static.
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
	 * @return 	object	A standard object with the passed label and value as properties.
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
	 * @param	array	$options	An array of option objects
	 * @param	string	$name		A string to use in the name attribute of the select tag.
	 * @param	string	$attribs	A string containing standard HTML attributes for the select tag. ie: 'class="myClass" multiple="multiple"'
	 * @param 	string	$selected	The selected value. This parameter is optional.
	 * @return	void
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
	 * Build and display a dialog box with content loaded via AJAX.
	 * 
	 * @param	string	$label	A string to print inside de link tag.
	 * @param	string	$target The target URL to load via AJAX.
	 * @param	int		$width	The dialog box width
	 * @param	int		$height	The dialog box height
	 * @param	bool	$form	If TRUE it shows save button with standard 'save' button
	 * @return	void
	 * @since 	1.0
	 */
	static function dialog($label, $target, $width=600, $height=560, $form=false) {
		$uid = uniqid();
		?>
		
		<script type="text/javascript">
		$(function() {
			$("#dialog_<?php echo $uid; ?>").dialog({
				autoOpen: false,
				bgiframe: false,
				width: <?php echo $width; ?>,
				height: <?php echo $height; ?>,
				modal: true,
				resizable: false
				<?php if ($form) : ?>
				,buttons: {
					"Save" : function() {
						submitbutton();
					},
					"Close" : function() {
						$(this).dialog('close');
					}
				}
				<?php endif; ?>
					
			});

			$(".loading").bind("ajaxSend", function() {
				$(this).show();
			})
			.bind("ajaxComplete", function() {
				   $(this).hide();
			});
			
			$('#dialog_trigger_<?php echo $uid; ?>').click(function(e) {
				e.preventDefault();
				$("#dialog_<?php echo $uid; ?>").load("<?php echo $target; ?>&tmpl=component");
				$("#dialog_<?php echo $uid; ?>").dialog('open');
			});
		});
		</script>
		
		<a id="dialog_trigger_<?php echo $uid; ?>" href="#"><?php echo $label; ?></a>
		
		<div id="dialog_<?php echo $uid; ?>" title="<?php echo $label; ?>">
			<div class="loading"></div>
		</div>
		
		<?php
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
	 * @since 	1.0
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
	 * @param 	string	$field_name		The name attribute fot the input tag
	 * @param	string	$attribs 		A string containing attributes for the input tag
	 * @param	array	$tokens			An array with the key/value pairs used to build the list of options
	 * @param	bool	$matchContains	Optional parameter (default: TRUE). If TRUE search matches inside string, 
	 * 									if FALSE only at the beginning.
	 * @return 	void
	 * @since	1.0
	 */
	static function autocomplete($field_name, $attribs, $tokens, $matchContains=true) {
		$document =& factory::getDocument('html');
		$document->addScript('lib/jquery/plugins/autocomplete/jquery.autocomplete.pack.js');
		$document->addStyleSheet('lib/jquery/plugins/autocomplete/jquery.autocomplete.css');
		
		$users_string = '';
		for ($i=0; $i<count($tokens); $i++) {
			if ($i>0) $users_string .= ";";
			$users_string .= $tokens[$i]['id']."|".$tokens[$i]['name'];
		}
		?>
		
		<textarea name="<?php echo $field_name; ?>_autocomplete" id="<?php echo $field_name; ?>_autocomplete" <?php echo $attribs; ?>></textarea>
		<input name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" type="hidden" />

		<script type="text/javascript">
			$(document).ready(function() {
				var data_string = "<?php echo $users_string; ?>".split(";");
				var data = new Array();

				for (count = 0; count < data_string.length; count++) {
					data[count] = data_string[count].split("|");
				}

				function formatItem(row) {
					return row[1];
				}
				
				function formatResult(row) {
					return row[1].replace(/(<.+?>)/gi, '');
				}
							
				
				$("#<?php echo $field_name; ?>_autocomplete").autocomplete(data, {
					multiple: true,
					matchContains: <?php echo $matchContains ? 'true' : 'false'; ?>,
					formatItem: formatItem,
					formatResult: formatResult
				});

				$("#<?php echo $field_name; ?>_autocomplete").result(function(event, data, formatted) {
					var hidden = $("#<?php echo $field_name; ?>");
					hidden.val( (hidden.val() ? hidden.val() + "," : hidden.val()) + data[0]);
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
	 * @return 	void
	 * @since 	1.0
	 */
	static function formToken() {
		?><input type="hidden" name="<?php echo crypt::getToken(); ?>" value="1" /><?php
	}
	
	/**
	 * Build an html button tag and echo it.
	 * 
	 * @param	string	$type		The button type. Possible values are 'button', 'submit', 'reset'
	 * @param	string	$label		A string to use as the button's label.
	 * @param 	string	$onclick	A string to be printed in the onclick attribute of the button tag.
	 * @return	void
	 * @since 	1.0
	 */
	static function button($type='button', $label='', $onclick='') {
		?><button class="ui-corner-all" type="<?php echo $type; ?>" onclick="<?php echo $onclick; ?>"><?php echo text::_( $label ); ?></button><?php
	}
	
	/**
	 * Build an html 'back' button tag and echo it.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	static function buttonBack() {
		?><button class="ui-corner-all" type="button" onclick="Javascript:window.history.back();"><?php echo text::_( _LANG_BACK ); ?></button> 	<?php
	}
	
	/**
	 * Build an html 'save' button tag and echo it.
	 * 
	 * This button will have an onclick attribute of 'submitbutton('save'); return false;'.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	static function buttonSave() {
		?><button class="ui-corner-all" type="button" onclick="submitbutton('save'); return false;"><?php echo text::_( _LANG_SAVE ); ?></button><?php
	}
	
	/**
	 * Build an html 'apply' button tag and echo it.
	 * 
	 * This button will have an onclick attribute of 'submitbutton('apply'); return false;'.
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	static function buttonApply() {
		?><button class="ui-corner-all" type="button" onclick="submitbutton('apply'); return false;"><?php echo text::_( _LANG_APPLY ); ?></button><?php
	}
	
	/**
	 * Redirects to previous page using Javascript window.history.back()
	 * 
	 * @return	void
	 * @since 	1.0
	 */
	static function historyBack() {
		?>
		<script type="text/javascript">
			window.history.back();
		</script>
		<?php  
	}
	
	/**
	 * Outputs message in Javascript alert box
	 *
	 * @param	string	$msg	A string containing the message to show in the alert box.
	 * @return	void
	 * @since 	1.0
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