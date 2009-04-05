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
class projectsModelFiles extends phpFrame_Application_Model {
	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 */
	function __construct() {
		//TODO: Check permissions
		parent::__construct();
	}
	
	public function getFiles($projectid) {
		$filter_order = phpFrame_Environment_Request::getVar('filter_order', 'f.ts');
		$filter_order_Dir = phpFrame_Environment_Request::getVar('filter_order_Dir', 'DESC');
		$search = phpFrame_Environment_Request::getVar('search', '');
		$search = strtolower( $search );
		$limitstart = phpFrame_Environment_Request::getVar('limitstart', 0);
		$limit = phpFrame_Environment_Request::getVar('limit', 20);

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
				  INNER JOIN (SELECT MAX(id) AS id FROM #__files GROUP BY parentid) ids ON f.id = ids.id "
				  . $where;
		//echo $query; exit;	  
		$this->db->setQuery( $query );
		$this->db->query();
		$total = $this->db->getNumRows();
		
		$pageNav = new phpFrame_HTML_Pagination( $total, $limitstart, $limit );

		// get the subset (based on limits) of required records
		$query .= $orderby." LIMIT ".$pageNav->limitstart.", ".$pageNav->limit;
		//echo $query; exit;
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		
		// Prepare rows and add relevant data
		if (is_array($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				// Get assignees
				$row->assignees = $this->getAssignees($row->id);
				
				// get total comments
				$modelComments =& $this->getModel('comments');
				$row->comments = $modelComments->getTotalComments($row->id, 'files');
					
				// Get older revisions
				$row->children = $this->_getOlderRevisions($row->parentid, $row->id);
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
	
	public function getFilesDetail($projectid, $fileid) {
		$query = "SELECT f.*, u.username AS created_by_name ";
		$query .= " FROM #__files AS f ";
		$query .= " JOIN #__users u ON u.id = f.userid ";
		$query .= " WHERE f.id = ".$fileid;
		$query .= " ORDER BY f.ts DESC";
		$this->db->setQuery($query);
		$row = $this->db->loadObject();
		
		if ($row === false) {
			return false;
		}
		
		// Get assignees
		$row->assignees = $this->getAssignees($fileid);
		
		// Get comments
		$modelComments =& $this->getModel('comments');
		$row->comments = $modelComments->getComments($projectid, 'files', $fileid);
		
		// Get older revisions
		$row->children = $this->_getOlderRevisions($row->parentid, $row->id);
				
		return $row;
	}
	
	/**
	 * Save a project file
	 * 
	 * This method uploads a project file and stores the relevant entry in the database.
	 * 
	 * @param	$post	The array to be used for binding to the row before storing it. Normally the HTTP_POST array.
	 * @return	mixed	Returns the stored table row object on success or FALSE on failure
	 */
	public function saveFile($post) {
		// Check whether a project id is included in the post array
		if (empty($post['projectid'])) {
			$this->error[] = _LANG_ERROR_NO_PROJECT_SELECTED;
			return false;
		}
		
		require_once COMPONENT_PATH.DS."tables".DS."files.table.php";		
		$row =& phpFrame::getInstance("projectsTableFiles");
		
		if (!$row->bind($post)) {
			$this->error[] = $row->getLastError();
			return false;
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
		$upload_dir = config::FILESYSTEM.DS."projects".DS.$post['projectid'].DS."files".DS;
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0711);
		}
		$accept = config::UPLOAD_ACCEPT; // mime types
		$max_upload_size = config::MAX_UPLOAD_SIZE*(1024*1024); // Mb
		$file = phpFrame_Utils_Filesystem::uploadFile('filename', $upload_dir, $accept, $max_upload_size);
		
		if (!empty($file['error'])) {
			$this->error[] = $file['error'];
			return false;
		}
		
		$row->filename = $file['file_name'];
		$row->filesize = $file['file_size'];
		$row->mimetype = $file['file_type'];
		
		$row->userid = $this->user->id;
		
		if (!$row->check()) {
			$this->error[] = $row->getLastError();
			return false;
		}
	
		if (!$row->store()) {
			$this->error[] = $row->getLastError();
			return false;
		}
		
		// Make parent files have their own id as their parentid
		if (empty($row->parentid)) {
			$row->parentid = $row->id;
			if (!$row->store()) {
				$this->error[] = $row->getLastError();
				return false;
			}
		}
		
		// File assignees are stored with a reference to the parentid, 
		// so they are assigned to the thread rather than individual files
		// Delete existing assignees before we store new ones if editing existing issue
		if ($row->revision > 0) {
			$query = "DELETE FROM #__users_files WHERE fileid = ".$row->parentid;
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		// Store assignees
		if (is_array($post['assignees']) && count($post['assignees']) > 0) {
			$query = "INSERT INTO #__users_files ";
			$query .= " (id, userid, fileid) VALUES ";
			for ($i=0; $i<count($post['assignees']); $i++) {
				if ($i>0) { $query .= ","; }
				$query .= " (NULL, '".$post['assignees'][$i]."', '".$row->parentid."') ";
			}
			$this->db->setQuery($query);
			$this->db->query();
		}
		
		return $row;
	}
	
	public function deleteFile($projectid, $fileid) {
		//TODO: This function should allow ids as either int or array of ints.
		//TODO: This function should also check permissions before deleting
		//TODO: This function should delete related items if any (comments, ...)
		
		// Instantiate table object
		require_once COMPONENT_PATH.DS."tables".DS."files.table.php";		
		$row =& phpFrame::getInstance("projectsTableFiles");
		
		// Load row data
		$row->load($fileid);
		
		// Delete file from filesystem
		unlink(config::FILESYSTEM.DS."projects".DS.$row->projectid.DS."files".DS.$row->filename);
		
		// Delete row from database
		if (!$row->delete($fileid)) {
			$this->error[] = $row->getLastError();
			return false;
		}
		else {
			return true;
		}
	}
	
	public function downloadFile($projectid, $fileid) {
		//TODO: This function should also check permissions
		require_once COMPONENT_PATH.DS."tables".DS."files.table.php";		
		$row =& phpFrame::getInstance("projectsTableFiles");
		
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
		$path_to_file = config::FILESYSTEM.DS."projects".DS.$row->projectid.DS."files".DS.$row->filename;
		$this->_readfile_chunked($path_to_file);
		exit;
	}
	
	/**
	 * Get list of assignees
	 *
	 * @param	int		$fileid
	 * @param	bool	$asoc
	 * @return	array	Asociative array with userid, name and email of assignees
	 */
	public function getAssignees($fileid, $asoc=true) {
		$query = "SELECT uf.userid, u.firstname, u.lastname, u.email";
		$query .= " FROM #__users_files AS uf ";
		$query .= "LEFT JOIN #__users u ON u.id = uf.userid";
		$query .= " WHERE uf.fileid = ".$fileid;
		$this->db->setQuery($query);
		$assignees = $this->db->loadObjectList();
		
		// Prepare assignee data
		for ($i=0; $i<count($assignees); $i++) {
			if ($asoc === false) {
				$new_assignees[$i] = $assignees[$i]->userid;
			}
			else {
				$new_assignees[$i]['id'] = $assignees[$i]->userid;
				$new_assignees[$i]['name'] = phpFrame_User_Helper::fullname_format($assignees[$i]->firstname, $assignees[$i]->lastname);
				$new_assignees[$i]['email'] = $assignees[$i]->email;
			}
		}
		
		return $new_assignees;
	}
	
	private function _readfile_chunked($filename, $retbytes=true) {
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
	
	private function _getOlderRevisions($parentid, $id) {
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