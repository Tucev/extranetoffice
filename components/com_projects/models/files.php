<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @subpackage	com_projects
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 * @author 		Luis Montero [e-noise.com]
 */

defined( '_EXEC' ) or die( 'Restricted access' );

/**
 * projectsModelFiles Class
 * 
 * @package		ExtranetOffice
 * @subpackage 	com_projects
 * @author 		Luis Montero [e-noise.com]
 * @since 		1.0
 * @see 		model
 */
class projectsModelFiles extends model {
	var $config=null;
	var $user=null;
	var $db=null;
	
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		$this->init();
		parent::__construct();
	}
	
	function init() {
		$this->config =& factory::getConfig();
		$this->user =& factory::getUser();
		$this->db =& factory::getDB(); // Instantiate joomla database object
		
		//TODO: Check permissions
	}
	
	function getFiles($projectid) {
		$filter_order = request::getVar('filter_order', 'f.ts');
		$filter_order_Dir = request::getVar('filter_order_Dir', 'DESC');
		$search = request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = request::getVar('limitstart', 0);
		$limit = request::getVar('limit', 20);

		$where = array();
		
		// Show only public projects or projects where user has an assigned role
		//TODO: Have to apply access levels
		//$where[] = "( p.access = '0' OR (".$this->user->id." IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";

		if ( $search ) {
			$where[] = "f.title LIKE '%".$this->db->getEscaped($search)."%'";
		}
		
		if (!empty($projectid)) {
			$where[] = "f.projectid = ".$projectid;	
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		if ($filter_order == 'f.ts'){
			$orderby = ' ORDER BY f.ts DESC';
		} else {
			$orderby = ' ORDER BY f.ts DESC, '. $filter_order .' '. $filter_order_Dir;
		}

		// get the total number of records
		// This query groups the files by parentid so and retireves the latest revision for each file in current project
		$query = "SELECT 
				  f.*, 
				  u.username AS created_by_name
				  FROM #__files AS f 
				  JOIN #__users u ON u.id = f.userid 
				  INNER JOIN (SELECT MAX(id) AS id FROM jos_intranetoffice_files GROUP BY parentid) ids ON f.id = ids.id "
				  . $where;
		//echo $query; exit;	  
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();

		
		$pageNav = new pagination( $total, $limitstart, $limit );

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo $query; exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Prepare assignee data
				if (!empty($row->assignees)) {
					$assignees = explode(',', $row->assignees);
					for ($i=0; $i<count($assignees); $i++) {
						$new_assignees[$i]['id'] = $assignees[$i];
						$new_assignees[$i]['name'] = usersHelperUsers::id2name($assignees[$i]);
					}
					$row->assignees = $new_assignees;
					unset($new_assignees);
				}
				
				// get total comments
				$modelComments =& $this->getModel('comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'files');
					
				// Get older revisions
				$row->children = $this->getOlderRevisions($row->parentid, $row->id);
			}
		}
		
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;
		
		// pack data into an array to return
		$return['rows'] = $rows;
		$return['pageNav'] = $pageNav;
		$return['lists'] = $lists;
		
		return $return;
	}
	
	function getFilesDetail($projectid, $fileid) {
		$query = "SELECT f.*, u.username AS created_by_name ";
		$query .= " FROM #__files AS f ";
		$query .= " JOIN #__users u ON u.id = f.userid ";
		$query .= " WHERE f.id = ".$fileid;
		$query .= " ORDER BY f.ts DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		// Get assignees
		$row->assignees = $this->getAssignees($fileid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'files', $fileid);
		
		// Get older revisions
		$row->children = $this->getOlderRevisions($row->parentid, $row->id);
				
		return $row;
	}
	
	function saveFile($projectid) {
		$row = new iOfficeTableFiles();
		
		$post = request::get( 'post' );
		if (!$row->bind( $post )) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Generate revision
		if (empty($row->parentid)) {
			$row->revision = 0;
		}
		else {
			$query = "SELECT revision FROM #__files ";
			$query .= " WHERE parentid = ".$row->parentid;
			$query .= " ORDER BY revision DESC LIMIT 0,1";
			$this->db->setQuery($query);
			$current_revision = $this->db->loadResult();
			$row->revision = ($current_revision+1);
		}
		
		// upload the file
		//TODO: Have to catch errors and look at file permissions
		$upload_dir = $this->config->get('filesystem').DS."projects".DS.$projectid.DS."files".DS;
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0711);
		}
		$accept = $this->config->get('upload_accept'); // mime types
		$max_upload_size = $this->config->get('max_upload_size')*(1024*1024); // Mb
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'enoise'.DS.'upload.php');
		$file = enoiseUpload::uploadFile('filename', $upload_dir, $accept, $max_upload_size);
		
		$row->filename = $file[0];
		$row->filesize = $file[1];
		$row->mimetype = $file[2];
		
		$row->userid = $this->user->id;
		
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}
	
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		// Make parent files have their own id as their parentid
		if (empty($row->parentid)) {
			$row->parentid = $row->id;
		}
		
		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		
		return $row;
	}
	
	function deleteFile($projectid, $fileid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		//TODO: This function should delete related items if any (comments, ...)
		
		// Instantiate table object
		$row = new iOfficeTableFiles();
		// Load row data
		$row->load($fileid);
		
		// Delete file from filesystem
		unlink($this->config->get('filesystem').DS."projects".DS.$row->projectid.DS."files".DS.$row->filename);
		
		// Delete row from database
		if (!$row->delete($fileid)) {
			JError::raiseError(500, $row->getError() );
			return false;
		}
		else {
			return true;
		}
	}
	
	function downloadFile($projectid, $fileid) {
		//TODO: This function should also check permissions
		$row = new iOfficeTableFiles();
		// Load row data
		$row->load($fileid);
		
		header("Content-type: ".$row->mimetype);
		header("Content-Disposition: attachment; filename=\"".$row->filename."\"");		
		header("Content-Length: ".$row->filesize);
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");
		ob_clean();
		flush();
		
		# begin download
		$path_to_file = $this->config->get('filesystem').DS."projects".DS.$row->projectid.DS."files".DS.$row->filename;
		$this->_readfile_chunked($path_to_file);
		exit;
	}
	
	function _readfile_chunked($filename, $retbytes=true) {
	   $chunksize = 1*(1024*1024); // how many bytes per chunk
	   $buffer = '';
	   $cnt =0;
	   $handle = fopen($filename, 'rb');
	   if ($handle === false) {
	       return false;
	   }
	   while (!feof($handle)) {
	       $buffer = fread($handle, $chunksize);
	       echo $buffer;
	       ob_flush();
	       flush();
	       if ($retbytes) {
	           $cnt += strlen($buffer);
	       }
	   }
	       $status = fclose($handle);
	   if ($retbytes && $status) {
	       return $cnt; // return num. bytes delivered like readfile() does.
	   }
	   return $status;
	}
	
	function getAssignees($fileid) {
		$query = "SELECT userid FROM #__users_files WHERE fileid = ".$fileid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadResultArray();
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			$new_assignees[$i]['id'] = $assignees[$i];
			$new_assignees[$i]['name'] = usersHelperUsers::id2name($assignees[$i]);
		}
		return $new_assignees;
	}
	
	function getOlderRevisions($parentid, $id) {
		// get children (older revisions)
		$query = "SELECT f.*, u.username AS created_by_name ";
		$query .= " FROM #__files AS f ";
		$query .= " JOIN #__users u ON u.id = f.userid";
		$query .= " WHERE f.parentid = ".$parentid." AND f.id <> ".$id;
		$query .= " ORDER BY f.ts DESC";
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
	}
}
?>