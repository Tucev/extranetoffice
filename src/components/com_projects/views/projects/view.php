<?php
/**
 * src/components/com_projects/views/projects/view.php
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
 * projectsViewProjects Class
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
class projectsViewProjects extends PHPFrame_MVC_View
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
        parent::__construct('projects', $layout);
    }
    
    /**
     * Override view display method
     * 
     * This method overrides the parent display() method and appends the page title 
     * to the document title.
     * 
     * @access public
     * @return void
     * @since  1.0
     */
    public function display()
    {
        // Set title in response document
        $this->getDocument()->setTitle(_LANG_PROJECTS);
        
        // Add pathway item
        $this->getPathway()->addItem(_LANG_PROJECTS, "index.php?component=com_projects");
        
        parent::display();
    }
    
    /**
     * Display project list layout
     * 
     * Custom display method triggered by list layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayProjectsList()
    {
        // Set page title in data array
        $this->addData('page_title', _LANG_PROJECTS);
    }
    
    /**
     * Display project detail layout
     * 
     * This method is a custom display method triggered by detail layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayProjectsDetail()
    {
        // Get project from data array (this has been set by the controller action
        $project = $this->_data['row'];
        // Build page heading
        $page_title = $project->name.' - '._LANG_PROJECTS_HOME;
        
        // Set page title and heading in view data
        $this->addData('page_title', $page_title);
        
        // Add pathway items
        $url = "index.php?component=com_projects&action=get_project_detail";
        $url .= "&projectid=".$project->id;
        $this->getPathway()->addItem($project->name, $url);
        $this->getPathway()->addItem(_LANG_PROJECTS_HOME);
        
        // Append page title to document title
        $this->getDocument()->appendTitle(' - '.$page_title);
    }
}
