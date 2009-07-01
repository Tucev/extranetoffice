<?php
/**
 * src/components/com_projects/views/files/view.php
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
 * projectsViewFiles Class
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
class projectsViewFiles extends PHPFrame_MVC_View
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
        parent::__construct('files', $layout);
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
            $tool_url = "index.php?component=com_projects&action=get_files";
            $tool_url .= "&projectid=".$project->id;
            $tool_url = PHPFrame_Utils_Rewrite::rewriteURL($tool_url);
            $this->addData("tool_url", $tool_url);
        
            // Append page title to document title
            $this->getDocument()->appendTitle(" - ".$project->name." - "._LANG_FILES);
            
            $this->addData('page_title', $project->name);
        
            // Add pathway item
            $this->getPathway()->addItem(_LANG_FILES, $tool_url);
        }
        
        parent::display();
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayFilesList()
    {
        $project = $this->_data['project'];
        
        $new_file_url = "index.php?component=com_projects&action=get_file_form";
        $new_file_url .= "&projectid=".$project->id;
        $new_file_url = PHPFrame_Utils_Rewrite::rewriteURL($new_file_url);
        $this->addData("new_file_url", $new_file_url);
    }
    
    /**
     * Custom display method triggered by form layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayFilesForm()
    {
        // Add pathway item
        $this->getPathway()->addItem(_LANG_FILES_NEW);
        
        // Append "new file"
        $this->getDocument()->appendTitle(" - "._LANG_FILES_NEW);
    }
    
    /**
     * Custom display method triggered by detail layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayFilesDetail()
    {
        $file_title = $this->_data['row']->title;
        
        // Add pathway item
        $this->getPathway()->addItem($file_title);
        
        // Append "new file"
        $this->getDocument()->appendTitle(" - ".$file_title);
    }
}
