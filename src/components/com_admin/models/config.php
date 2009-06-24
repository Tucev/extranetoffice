<?php
/**
 * src/components/com_admin/models/config.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

/**
 * adminModelConfig Class
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @subpackage com_admin
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 * @see        PHPFrame_MVC_Model
 * @since      1.0
 */
class adminModelConfig extends PHPFrame_MVC_Model
{
    /**
     * Update configuration file using data passed in request
     * 
     * @param array $post The post array with the config data to save.
     * 
     * @access public
     * @return bool Returns TRUE on success or FALSE if it fails.
     * @since  1.0
     */
    function saveConfig($post)
    {
        $fname = _ABS_PATH.DS."src".DS."config.php";
        // Open file for reading first
        if (!$fhandle = fopen($fname, "r")) {
            $this->_error[] = 'Error opening file '.$fname.'.';
            return false;
        }
        
        // Read content of file into string
        if (!$content = fread($fhandle, filesize($fname))) {
            $this->_error[] = 'Error reading file '.$fname.'.';
            return false;
        }
        
        // Use reflection API to get array of class constants
        $reflection_config = new ReflectionClass(config);
        $config_constants = $reflection_config->getConstants();
        
        // Loop through all config properties and build arrays with 
        // patterns and replacements for regex
        foreach ($config_constants as $key=>$value) {
            $lowercase_key = strtolower($key);
            if (isset($post[$lowercase_key]) && $post[$lowercase_key] != "") {
                $patterns[] = '/const '.$key.'=(.*);/';
                $replacements[] = 'const '.$key.'="'.$post[$lowercase_key].'";';    
            }
        }
        
        // Replace config vars in config file contents
        $content = preg_replace($patterns, $replacements, $content);
        
        // Reopen file for writing
        PHPFrame_Utils_Filesystem::write($fname, $content);

        return true;
    }
}
