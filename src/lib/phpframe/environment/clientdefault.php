<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	environment
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );
	
/**
 * Client used by default (PC HTTP browsers or anything for which no helper exists) 
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
class phpFrame_Environment_ClientDefault implements phpFrame_Environment_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used
	 * 
	 * @static
	 * @access	public
	 * @return	object instance of this class  
	 */
	public static function detect() {
		
		//TODO test checking for $_SERVER.HTTP_USER_AGENT
		
		//this is our last hope to find a helper, just return instance
		return new self;
	}
	
	/**	
	 * Populate the Unified Request array
	 * 
	 * @access	public
	 * @return	Unified Request Array
	 */
	public function populateURA() {
	
		$request = array();
		
		// Get an instance of PHP Input filter
		$inputfilter = new InputFilter();
			
		// Process incoming request arrays and store filtered data in class
		$request['request'] = $inputfilter->process($_REQUEST);
		$request['get'] = $inputfilter->process($_GET);
		$request['post'] = $inputfilter->process($_POST);
			
		// Once the superglobal request arrays are processed we unset them
		// to prevent them being used from here on
		unset($_REQUEST, $_GET, $_POST);
		
		return $request;
	}
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string name to identify helper type
	 */
	public function getName() {
		return "default";
	}
	
	public function preActionHook() {
		// add the jQuery + jQuery UI libraries to the HTML document
		// that we will use in the response. jQuery lib need to be loaded before 
		// we load the jQuery plugins in the component output.
		$document = phpFrame::getDocument('html');
		$document->addScript('lib/jquery/js/jquery-1.3.2.min.js');
		$document->addScript('lib/jquery/js/jquery-ui-1.7.custom.min.js');
		$document->addScript('lib/jquery/plugins/validate/jquery.validate.pack.js');
		$document->addScript('lib/jquery/plugins/form/jquery.form.pack.js');
		$document->addStyleSheet('lib/jquery/css/extranetoffice/jquery-ui-1.7.custom.css');
	}
	
	public function renderView($data) {
		if (!empty($data['view'])) {
    		$tmpl_path = COMPONENT_PATH.DS."views".DS.$data['view'].DS."tmpl".DS.$this->getName();
    		if (!empty($data['layout'])) {
    			$tmpl_path .= "_".$data['layout'];
    		}
    		$tmpl_path .= ".php";
    		
    		if (is_file($tmpl_path)) {
    			require_once $tmpl_path;
    		}
    		else {
    			throw new phpFrame_Exception("Layout template file ".$tmpl_path." not found.");
    		}
    	}
	}
	
	public function renderTemplate(&$str) {
		// Make modules available to templates
		$modules = phpFrame::getModules();
		
		// If tmpl flag is set to component in request it means that
		// we dont need to wrap the component output in the overall template
		// so we just prepend the sytem events and return
		if (phpFrame_Environment_Request::getVar('tmpl') == 'component') {
			$sys_events = $modules->display('sysevents', '_sysevents');
			$str = $sys_events.$str;
			return;
		}
		
		// Instantiate document object to make available in template scope
		$document = phpFrame::getDocument('html');
		// get pathway
		$pathway = phpFrame::getPathway();
		
		// Set file name to load depending on session auth
		$session = phpFrame::getSession();
		if (!$session->isAuth()) {
			$template_filename = 'login.php';
		}
		else {
			$template_filename = 'index.php';
		}
		
		$template_path = _ABS_PATH.DS.'templates'.DS.config::TEMPLATE;
		
		// Make copy of component output string to use in template
		$component_output = $str;
		
		// Start buffering
		ob_start();
		require_once $template_path.DS.$template_filename;
		// save buffer in string passed by reference
		$str = ob_get_contents();
		// clean output buffer
		ob_end_clean();
	}
}
