<?php
/**
 * src/components/com_projects/views/issues/view.php
 * 
 * PHP version 5
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @copyright  2009 E-noise.com Limited
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id$
 * @link       http://code.google.com/p/extranetoffice/source/browse
 */

/**
 * projectsViewIssues Class
 * 
 * @category   Project_Management
 * @package    ExtranetOffice
 * @subpackage com_projects
 * @author     Luis Montero <luis.montero@e-noise.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://code.google.com/p/extranetoffice/source/browse
 * @see        PHPFrame_MVC_View
 * @since      1.0
 */
class projectsViewIssues extends PHPFrame_MVC_View
{
    /**
     * Constructor
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function __construct($layout)
    {
        // Invoke the parent to set the view name and default layout
        parent::__construct('issues', $layout);
    }
    
    /**
     * Override view display method
     * 
     * This method overrides the parent display() method and appends the page 
     * title to the document title.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function display()
    {
        // Set title in response document
        $this->getDocument()->setTitle(_LANG_PROJECTS);
        
        // Add component wide pathway item
        $url = "index.php?component=com_projects";
        $this->getPathway()->addItem(_LANG_PROJECTS, $url);
        
        // Add project specific pathway item
        $project = $this->_data['project'];
        $projectid = $project->id;
        if (!empty($projectid)) {
            // Add url to project home
            $project_url = "index.php?component=com_projects&action=get_project_detail";
            $project_url .= "&projectid=".$project->id;
            $project_url = PHPFrame_Utils_Rewrite::rewriteURL($project_url);
            $this->addData("project_url", $project_url);
            
            // Add project wide pathway item
            $this->getPathway()->addItem($project->name, $project_url);
            
            // Add url to tool list
            $tool_url = "index.php?component=com_projects&action=get_issues";
            $tool_url .= "&projectid=".$project->id;
            $tool_url = PHPFrame_Utils_Rewrite::rewriteURL($tool_url);
            $this->addData("tool_url", $tool_url);
        
            // Append page title to document title
            $this->getDocument()->appendTitle(" - ".$project->name." - "._LANG_ISSUES);
            
            $this->addData('page_title', $project->name);
        
            // Add pathway item
            $this->getPathway()->addItem(_LANG_ISSUES, $tool_url);
        }
        
        parent::display();
    }
    
    /**
     * Custom display method triggered by list layout
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayIssuesList()
    {
        $project = $this->_data['project'];
        
        $new_issue_url = "index.php?component=com_projects&action=get_issue_form";
        $new_issue_url .= "&projectid=".$project->id;
        $new_issue_url = PHPFrame_Utils_Rewrite::rewriteURL($new_issue_url);
        $this->addData("new_issue_url", $new_issue_url);
    }
    
    /**
     * Custom display method triggered by form layout
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayIssuesForm()
    {
        $issueid = $this->_data['row']->id;
        $action = empty($issueid) ? _LANG_ISSUES_NEW : _LANG_ISSUES_EDIT;
        
        // Add pathway item
        $this->getPathway()->addItem($action);
        
        // Append "new file"
        $this->getDocument()->appendTitle(" - ".$action);
    }
    
    /**
     * Custom display method triggered by detail layout
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayIssuesDetail()
    {
        //$this->_data['page_title'] .= ' - '.$this->_data['row']->title;
        //$this->getPathway()->addItem(_LANG_ISSUES, PHPFrame_Utils_Rewrite::rewriteURL("index.php?component=com_projects&view=issues&projectid=".$this->_data['project']->id));
        //$this->getPathway()->addItem($this->_data['row']->title);
    }
}
