<?php
/**
 * @version		$Id$
 * @package		phpFrame
 * @subpackage 	utils
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * Filesystem Class
 * 
 * @package		phpFrame
 * @subpackage 	utils
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 */
class phpFrame_Utils_Filesystem {
	/**
	 * Upload file
	 * 
	 * @param	string	$fieldName
	 * @param	string	$dir
	 * @param	string	$accept
	 * @param	int		$max_upload_size
	 * @param	bool	$overwrite
	 * @return 	mixed	An assoc array containing file_name, file_size and file_type or an assoc array containing error on failure.
	 */
	static function uploadFile($fieldName, $dir, $accept="*", $max_upload_size=0, $overwrite=false) {
		// Get file data from request
		$file_tmp = $_FILES[$fieldName]['tmp_name']; // $file_tmp is where file went on webserver
		$file_name = $_FILES[$fieldName]['name']; // $file_tmp_name is original file name
		$file_size = $_FILES[$fieldName]['size']; // $file_size is size in bytes
		$file_type = $_FILES[$fieldName]['type']; // $file_type is mime type e.g. image/gif
		$file_error = $_FILES[$fieldName]['error']; // $file_error is any error encountered
		
		// Declare array to be used for return
		$array = array();
		
		// check for generic errors first		  
		if ($file_error > 0) {
			switch ($file_error) {
			  case 1:  $array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_PHP_UP_MAX_FILESIZE;
			  case 2:  $array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_PHP_MAX_FILESIZE;
			  case 3:  $array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_PARTIAL_UPLOAD;
			  case 4:  $array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_NO_FILE;
			}
			return $array;
		}
		
		// check custom max_upload_size passed into the function
		if (!empty($max_upload_size) && $max_upload_size < $file_size) {
			$array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_MAX_FILESIZE;
			$array['error'] .= ' max_upload_size: '.$max_upload_size.' | file_size: '.$file_size;
			return $array;
		}
		
		// Checkeamos el MIME type con la lista que formatos validos ($accept - valores separados por ',')
		if ($accept != "*") {
			$valid_file_types = explode(",", $accept);
			$type_ok = 0;
			
			foreach ($valid_file_types as $type) {
				if ($file_type == $type) {
					$type_ok = 1;
				}
			}
			
			if ($type_ok == 0) {
				$array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_FILETYPE;
				return $array;
			}	
		}
		
		// CHECK FOR SPECIAL CHARACTERS
		$special_chars = array('á','ä','à','é','ë','è','í','ï','ì','ó','ö','ò','ú','ü','ù','Á','Ä','À','É','Ë','È','Í','Ï','Ì','Ó','Ö','Ò','Ú','Ü','Ù','ñ','Ñ','?','¿','!','¡','(',')','[',']',',');
		foreach ($special_chars as $special_char) {
			$file_name = str_replace($special_char, '', $file_name);
		}
		
		// BEFORE WE MOVE THE FILE TO IT'S TARGET DIRECTORY 
		// WE CHECK IF A FILE WITH THE SAME NAME EXISTS IN THE TARGET DIRECTORY
		if ($overwrite === false) {
		  $check_if_file_exists = file_exists($dir.DS.$file_name);
		  if ($check_if_file_exists === true) {
			// split file name into name and extension
			$split_point = strrpos($file_name, '.');
			$file_n = substr($file_name, 0, $split_point);
			$file_ext = substr($file_name, $split_point);
			$i=0;
			while (true === file_exists($dir.DS.$file_n.$i.$file_ext)) {
				$i++;
			}
			$file_name = $file_n.$i.$file_ext;
		  }
		}
		
		// put the file where we'd like it
		$path = $dir.DS.$file_name;
		// is_uploaded_file and move_uploaded_file added at version 4.0.3
		if (is_uploaded_file($file_tmp)) {
			if (!move_uploaded_file($file_tmp, $path)) {
				$array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_MOVE;
				return $array;
			}
		} 
		else {
			$array['error'] = _PHPFRAME_LANG_UPLOAD_ERROR_ATTACK.' '.$file_name;
			return $array;
		}
		
		$array = array('file_name' => $file_name, 'file_size' => $file_size, 'file_type' => $file_type, 'error' => '');
		return $array;
	}
}
?>
