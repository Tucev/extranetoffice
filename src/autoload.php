<?php
/**
 * src/autoload.php
 * 
 * PHP version 5
 * 
 * @category   MVC_Framework
 * @package    PHPFrame_Scaffold
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/phpframe/source/browse/#svn/PHPFrame_Scaffold
 */

spl_autoload_register('__autoload');

/**
 * Autoload magic method
 * 
 * This method is automatically called in case you are trying to use a class/interface which 
 * hasn't been defined yet. By calling this function the scripting engine is given a last 
 * chance to load the class before PHP fails with an error. 
 * 
 * @param string $class_name The class name to load.
 * 
 * @access public
 * @return void
 * @since  1.0
 */
function __autoload($class_name) {
    // PHPMailer
    if ($class_name == 'PHPMailer') {
        $file_path = _ABS_PATH.DS."lib".DS."phpmailer".DS."phpmailer.php";
    
    // PHPInputFilter
    } elseif ($class_name == 'InputFilter') {
        $file_path = _ABS_PATH.DS."lib".DS."phpinputfilter".DS."inputfilter.php";
    
    } elseif ($class_name == 'vCard') {
        $file_path = _ABS_PATH.DS."lib".DS."bitfolge".DS."vcard.php";
    
    } else {
        // Components classes
        $pattern = '/^([a-z]+)([A-Z]{1}[a-z]+)([A-Z]{1}[a-z]+)?([A-Z]{1}[a-z]+)?$/';
        preg_match($pattern, $class_name, $matches);
        if (is_array($matches) && count($matches) > 1) {
            $file_path = _ABS_PATH.DS."src".DS."components".DS;
            $file_path .= "com_".strtolower($matches[1]).DS;
            
            switch ($matches[2]) {
                case 'Controller' : 
                    $file_path .= "controller.php";
                    break;
                case 'Model' : 
                    $file_path .= "models".DS.strtolower($matches[3]).".php";
                    break;
                case 'View' : 
                    if ($matches[3] == 'Helper') {
                        $file_path .= "views".DS.strtolower($matches[3]).".php";
                    } else {
                        $file_path .= "views".DS.strtolower($matches[3]).DS."view.php";
                    }
                    
                    break;
                case 'Helper' : 
                    $file_path .= "helpers".DS.strtolower($matches[3]).".helper.php";
                    break;
                case 'Table' : 
                    $file_path .= "tables".DS.strtolower($matches[3]);
                    if (isset($matches[4])) $file_path .= "_".strtolower($matches[4]);
                    $file_path .=".table.php";
                    break;
            }
        }
    }
    
    // require the file if it exists, otherwise we throw an exception
    if (is_file($file_path)) {
        @include_once $file_path;
    }
}
