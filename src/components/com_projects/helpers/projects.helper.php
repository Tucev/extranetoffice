<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage     com_projects
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

class projectsHelperProjects
{
    /**
     * Translate projectid to name
     * 
     * @param     int        The ID to be translated
     * @return     string    If no id is passed returns false, otherwise returns the project name as a string
     */
    public static function id2name($id=0)
    {
        if (!empty($id)) { // No category has been selected
            $db = PHPFrame::DB();
            $sql = "SELECT name FROM #__projects WHERE id = :id";
            return $db->fetchColumn($sql, array(":id"=>$id));
        } else {
            return false;
        }
    }
    
    /**
     * Function to build HTML select of projects
     * 
     * @param    int        The selected value if any
     * @param     string    Attributes for the <select> tag
     * @return     string    A string with the HTML select
     */
    public static function select($selected=0, $attribs='')
    {
        // assemble projects into the array
        $options = array();
        $options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select a Project --' ) );
        
        // get projects from db
        $db = PHPFrame::DB();
        $user = PHPFrame::Session()->getUser();
        
        $sql = "SELECT p.id, p.name ";
        $sql .= "FROM #__projects AS p ";
        $sql .= " WHERE ( p.access = '0' OR (:userid IN (SELECT userid FROM #__users_roles WHERE projectid = p.id) ) )";
        $sql .= " ORDER BY p.name ASC";
        
        $rows = $db->fetchObjectList($sql, array(":userid"=>$user->id));
        
        foreach ($rows as $row) {
            $options[] = PHPFrame_HTML::_('select.option', $row->id, $row->name );
        }
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, 'projectid', $attribs, $selected);
        return $output;
    }
    
    public static function project_typeid2name($id=0)
    {
        if (!empty($id)) { // No category has been selected
            $db = PHPFrame::DB();
            $sql = "SELECT name FROM #__project_types WHERE id = :id";
            return $db->fetchColumn($sql, array(":id"=>$id));
        } else {
            return false;
        }
    }
    
    public static function project_type_select($selected=0, $attribs='')
    {
        // assemble project types into the array
        $options = array();
        $options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select a Project Type --' ) );
        
        // get project_types from db
        $db = PHPFrame::DB();
        $query = "SELECT id, name FROM #__project_types ";
        $query .= " ORDER BY name";
        $rows = $db->fetchObjectList($query);
        
        foreach ($rows as $row) {
            $options[] = PHPFrame_HTML::_('select.option', $row->id, $row->name );
        }
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, 'project_type', $attribs, $selected);
        return $output;
    }
    
    public static function priorityid2name($priorityid)
    {
        switch ($priorityid) {
            case '0' :
                return _LANG_PROJECTS_PRIORITY_LOW;
            case '1' :
                return _LANG_PROJECTS_PRIORITY_MEDIUM;
            case '2' :
                return _LANG_PROJECTS_PRIORITY_HIGH;
        }
    }
    
    public static function priority_select($selected=0, $attribs='')
    {
        // assemble priorities into the array
        $options = array();
        
        $options[] = PHPFrame_HTML::_('select.option', 0, _LANG_PROJECTS_PRIORITY_LOW );
        $options[] = PHPFrame_HTML::_('select.option', 1, _LANG_PROJECTS_PRIORITY_MEDIUM );
        $options[] = PHPFrame_HTML::_('select.option', 2, _LANG_PROJECTS_PRIORITY_HIGH );

        $output = PHPFrame_HTML::_('select.genericlist', $options, 'priority', $attribs, $selected);
        
        return $output;
    }
    
    public static function global_accessid2name($accessid)
    {
        switch ($accessid) {
            case '0' :
                return _LANG_PROJECTS_ACCESS_PUBLIC;
            case '1' :
                return _LANG_PROJECTS_ACCESS_PRIVATE;
        }
    }
    
    public static function global_access_select($fieldname='access', $selected=1, $attribs='')
    {
        // assemble access into the array
        $options = array();
        //$options[] = PHPFrame_HTML::_('select.option', '', PHPFrame_Base_String::html( '-- Select an Access Level --' ) );
        
        $options[] = PHPFrame_HTML::_('select.option', 0, _LANG_PROJECTS_ACCESS_PUBLIC );
        $options[] = PHPFrame_HTML::_('select.option', 1, _LANG_PROJECTS_ACCESS_PRIVATE );
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, $fieldname, $attribs, $selected);
        return $output;
    }
    
    public static function accessid2name($accessid)
    {
        switch ($accessid) {
            case '1' :
                return _LANG_PROJECTS_ACCESS_ADMINS;
            case '2' :
                return _LANG_PROJECTS_ACCESS_WORKERS;
            case '3' :
                return _LANG_PROJECTS_ACCESS_GUESTS;
            case '4' :
                return _LANG_PROJECTS_ACCESS_PUBLIC;
        }
    }
    
    public static function access_select($fieldname='access', $selected=0, $attribs='')
    {
        // assemble access into the array
        $options = array();
        $options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select an Access Level --' ) );
        
        $options[] = PHPFrame_HTML::_('select.option', 1, _LANG_PROJECTS_ACCESS_ADMINS );
        $options[] = PHPFrame_HTML::_('select.option', 2, _LANG_PROJECTS_ACCESS_WORKERS );
        $options[] = PHPFrame_HTML::_('select.option', 3, _LANG_PROJECTS_ACCESS_GUESTS );
        $options[] = PHPFrame_HTML::_('select.option', 4, _LANG_PROJECTS_ACCESS_PUBLIC );
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, $fieldname, $attribs, $selected);
        return $output;
    }
    
    public static function statusid2name($statusid)
    {
        switch ($statusid) {
            case '0' :
                return _LANG_PROJECTS_STATUS_PLANNING;
            case '1' :
                return _LANG_PROJECTS_STATUS_IN_PROGRESS;
            case '2' :
                return _LANG_PROJECTS_STATUS_PAUSED;
            case '3' :
                return _LANG_PROJECTS_STATUS_FINISHED;
            case '-1' :
                return _LANG_PROJECTS_STATUS_ARCHIVED;
        }
    }
    
    public static function status_select($selected=0, $attribs='')
    {
        // assemble access into the array
        $options = array();
        $options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select an Status --' ) );
        
        $options[] = PHPFrame_HTML::_('select.option', '0', _LANG_PROJECTS_STATUS_PLANNING );
        $options[] = PHPFrame_HTML::_('select.option', '1', _LANG_PROJECTS_STATUS_IN_PROGRESS );
        $options[] = PHPFrame_HTML::_('select.option', '2', _LANG_PROJECTS_STATUS_PAUSED );
        $options[] = PHPFrame_HTML::_('select.option', '3', _LANG_PROJECTS_STATUS_FINISHED );
        $options[] = PHPFrame_HTML::_('select.option', '-1', _LANG_PROJECTS_STATUS_ARCHIVED );
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, 'status', $attribs, $selected);
        return $output;
    }
    
    /**
     * Build and display an input tag with project members autocompleter
     * 
     * @param    int        $projectid
     * @param    bool    $members    If TRUE it shows project members, if FALSE it shows non-project members
     * @return    void
     */
    public static function autocompleteMembers($projectid, $members=true)
    {
        $db = PHPFrame::DB();
        $query = "SELECT u.id, u.username, u.firstname, u.lastname ";
        $query .= "FROM #__users AS u ";
        $query .= " WHERE u.id ";
        if (!$members)  $query .= " NOT ";
        $query .= " IN (SELECT u.id FROM #__users AS u LEFT JOIN #__users_roles ur ON ur.userid = u.id WHERE ur.projectid = ".$projectid.")";
        $query .= " AND (u.deleted = '0000-00-00 00:00:00' OR u.deleted IS NULL)";
        $query .= " ORDER BY u.username";
        
        if (!$rows = $db->fetchObjectList($query)) {
          return _LANG_PROJECTS_NO_EXISTING_MEMBERS;
        }
        
        // Organise rows into array of arrays instead of array of objects
        foreach ($rows as $row) {
            $tokens[] = array('id' => $row->id, 'name' => $row->firstname." ".$row->lastname." (".$row->username.")");
        }
        
        PHPFrame_HTML::autocomplete('userids', 'cols="60" rows="2"', $tokens);
    }
    
    public static function project_roleid2name($id=0)
    {
        if (!empty($id)) { // No category has been selected
            $db = PHPFrame::DB();
            $sql = "SELECT name FROM #__roles WHERE id = :id";
            return $db->fetchColumn($sql, array(":id"=>$id));
        } else {
            return false;
        }
    }
    
    public static function project_role_select($selected=0, $attribs='')
    {
        // assemble project types into the array
        $options = array();
        //$options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select a Role --' ) );
        
        // get project_types from db
        $db = PHPFrame::DB();
        $query = "SELECT id, name FROM #__roles ";
        $query .= " ORDER BY id ASC";
        $rows = $db->fetchObjectList($query);
        
        foreach ($rows as $row) {
            $options[] = PHPFrame_HTML::_('select.option', $row->id, $row->name );
        }
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, 'roleid', $attribs, $selected);
        return $output;
    }
    
    public static function issue_typeid2name($id=0)
    {
        if (!empty($id)) { // No category has been selected
            $db = PHPFrame::DB();
            $sql = "SELECT name FROM #__issue_types WHERE id = :id";
            return $db->fetchColumn($sql, array(":id"=>$id));
        } else {
            return false;
        }
    }
    
    public static function issue_type_select($selected=0, $attribs='')
    {
        // assemble project types into the array
        $options = array();
        $options[] = PHPFrame_HTML::_('select.option', '0', PHPFrame_Base_String::html( '-- Select an issue type (optional) --' ) );
        
        // get project_types from db
        $db = PHPFrame::DB();
        $query = "SELECT id, name FROM #__issue_types ";
        $query .= " ORDER BY id ASC";
        
        $rows = $db->fetchObjectList($query);
        
        if (is_array($rows) && count($rows) > 0) {
            foreach ($rows as $row) {
                $options[] = PHPFrame_HTML::_('select.option', $row->id, $row->name );
            }
        }
        
        $output = PHPFrame_HTML::_('select.genericlist', $options, 'issue_type', $attribs, $selected);
        return $output;
    }
    
    public static function fileid2name($id=0)
    {
        if (!empty($id)) { // No file has been selected
            $db = PHPFrame::DB();
            $sql = "SELECT title FROM #__files WHERE id = :id";
            return $db->fetchColumn($sql, array(":id"=>$id));
        } else {
            return false;
        }
    }
    
    public static function activitylog_type2printable($type)
    {
        switch ($type) {
            case 'issues' :
                return _LANG_ISSUE;
            case 'messages' :
                return _LANG_MESSAGE;
            case 'milestones' :
                return _LANG_MILESTONE;
            case 'files' :
                return _LANG_FILE;
            case 'meetings' :
                return _LANG_MEETING;
            case 'comments' :
                return _LANG_COMMENTS;
        }
    }
    
    public static function mimetype2icon($mimetype)
    {
        switch ($mimetype) {
            case 'image/jpg' :
            case 'image/jpeg' :
            case 'image/png' :
            case 'image/gif' :
                return 'image.png';
                break;
                
            case 'application/pdf' :
                return 'pdf.png';
                break;
                
            case 'application/msword' :
            case 'application/vnd.oasis.opendocument.text' :
                return 'writer.png';
                break;
                
            default :
                return 'x.png';
                break;
        }
    }
}
