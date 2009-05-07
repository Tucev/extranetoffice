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
 * Client for XML Remote Procedure Call
 * 
 * @todo		
 * @package		
 * @subpackage 	
 * @author 		
 * @since 		
 */
class phpFrame_Client_Xmlrpc implements phpFrame_Client_IClient {
		
	/**
	 * Check if this is the correct helper for the client being used
	 * 
	 * @static
	 * @access	public
	 * @return	mixed object instance of this class if correct helper for client or false otherwise
	 */
	public static function detect() {
		
		global $HTTP_RAW_POST_DATA;
		//check existance of $_HTTP_RAW_POST_DATA array
		if (count($HTTP_RAW_POST_DATA) > 0) {
			//check for a valid XML structure
			$domDocument = new DOMDocument;
            if ($domDocument->loadXML($HTTP_RAW_POST_DATA))
			{
				$domXPath = new DOMXPath($domDocument);
				//check for valid RPC
				if ($domXPath->query("methodCall/params/param")->$length > 0) //xpath for methodname and component
				{
					return new self;
				}
			}
		}
		return false;
	}
	
	/**	
	 * Populate the Unified Request array
	 * 
	 * @access	public
	 * @return	Unified Request Array
	 */
	public function populateURA() {
		global $HTTP_RAW_POST_DATA;
		
ob_start();
var_dump($this->_parseXMLRPC($HTTP_RAW_POST_DATA));
$output = ob_get_contents();
ob_end_clean();
file_put_contents("/var/www/xmlrpc/globals.html",$output);
exit;	
		
		return $this->_parseXMLRPC($HTTP_RAW_POST_DATA);
		
	}
	
	/**	
	 * Get helper name
	 * 
	 * @access	public
	 * @return	string name to identify helper type
	 */
	public function getName() {
		return "xmlrpc";
	}
	
	public function preActionHook() {}
	
	public function renderView($data) {}
	
	public function renderTemplate(&$str) {}
	
	/**
     * This method is used to parse an XML remote procedure call
     *
     * @access private
     * @param string $xml A string containing the XML call.
     * @return array A nice asociative array with all the data.
     */
    private function _parseXMLRPC($xml) {
        $domDocument = new DOMDocument;
        $domDocument->loadXML($xml);
        
        $domXPath = new DOMXPath($domDocument);
        $query = "//methodCall/params/param";
        $nodes = $domXPath->query($query);
        $array = $this->_parseXMLRPCRecurse($domXPath, $nodes);
    	return $array;
    }
       
    /**
     * This method is used by _parseXMLResponse() to loop recursively through XML nodes and collect data.
     *
     * @access private
     * @param object $domXPath The DOMXPath object used for parsing the XML. This object is created in _parseXMLResponse().
     * @param object $nodes
     * @param string $search_path
     * @param array $array
     * @return array
     */
    private function _parseXMLRPCRecurse(&$domXPath, $nodes, $search_path="", &$array=array()) {
        foreach ($nodes as $node) {
            if ($node->childNodes->length > 1) {
                $query = "params/param";
                $children = $domXPath->query($query, $node);
                $this->_parseXMLResponseRecurse($domXPath, $children, $query, $array[$node->getAttribute("key")]);
                      
                $query = "dt_array/item";
                $children = $domXPath->query($query, $node);
                $this->_parseXMLResponseRecurse($domXPath, $children, $query, $array[$node->getAttribute("key")]);
            }
            else {
                       $array[$node->getAttribute("key")] = $node->nodeValue;
            }
        }
        return $array;
    }
}
