<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_user
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * userModelUser Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_user
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class userModelUser extends model {
	/**
	 * Save current user
	 * 
	 * @return	bool	Returns TRUE on success or FALSE on failure.
	 */
	function saveUser() {
		$userid = request::getVar('id', null);
		
		// Get reference to user object
		$user =& factory::getUser();
		//$user->load($userid, 'password');
		
		$post = request::get('post');
		
		// Upload image if photo sent in request
		if (!empty($_FILES['photo']['name'])) {
			$dir = _ABS_PATH.DS.$this->config->upload_dir.DS."users";
			$accept = 'image/jpeg,image/jpg,image/png,image/gif';
			$upload = filesystem::uploadFile('photo', $dir, $accept);
			if (!empty($upload['error'])) {
				$this->error[] = $upload['error'];
				return false;
			}
			else {
				// resize image
				$image = new image();
				$image->resize_image($dir.DS.$upload['file_name'], $dir.DS.$upload['file_name'], 80, 110);
				// Store file name in post array
				$post['photo'] = $upload['file_name'];
			}
		}
		
		// exlude password if not passed in request
		$exclude = '';
		if (empty($post['password'])) {
			$exclude = 'password';
		}
		
		// Bind the post data to the row array
		if ($user->bind($post, $exclude) === false) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		if (!$user->check()) {
			$this->error[] = $user->getLastError();
			return false;
		}
	
		if (!$user->store()) {
			$this->error[] = $user->getLastError();
			return false;
		}
		
		return true;
	}
}
?>