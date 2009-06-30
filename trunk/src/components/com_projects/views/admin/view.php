<?php
/**
 * src/components/com_projects/views/admin/view.php
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
 * projectsViewAdmin Class
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
class projectsViewAdmin extends PHPFrame_MVC_View
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
        parent::__construct('admin', $layout);
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
            $url = "index.php?component=com_projects&action=get_project_detail";
            $url .= "&projectid=".$project->id;
            $this->getPathway()->addItem($project->name, $url);
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
    protected function displayAdminList()
    {
        $project = $this->_data['project'];
        $page_title = $project->name.' - '._LANG_ADMIN;
        
        $this->addData('page_title', $page_title);
        
        // Add pathway item
        $this->getPathway()->addItem(_LANG_ADMIN);
        
        // Append page title to document title
        $this->getDocument()->appendTitle(" - ".$page_title);
    }
    
    /**
     * Display project form layout
     * 
     * This method is a custom display method triggered by detail layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayAdminForm()
    {
        $project = $this->_data['project'];
        $projectid = $project->id;
        
        if (!empty($projectid)) {
            $page_title = _LANG_PROJECTS_EDIT;
            $document_title = $project->name." - ".$page_title;
        } else {
            $page_title = _LANG_PROJECTS_NEW;
            $document_title = $page_title;
        }
        
        $this->addData('page_title', $page_title);
        
        // Add pathway item
        $this->getPathway()->addItem($page_title);
        
        // Append page title to document title
        $this->getDocument()->appendTitle(" - ".$document_title);
    }
    
    /**
     * Display project member role layout
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayAdminMemberRole()
    {
        $project = $this->_data['project'];
        
        $page_title = $project->name." - ". _LANG_ADMIN;
        
        $this->addData('page_title', $page_title);
        
        // Add pathway item
        $this->getPathway()->addItem(_LANG_PROJECTS_MEMBERS);
        
        // Append page title to document title
        $this->getDocument()->appendTitle(" - ".$page_title);
    }
    
    /**
     * Set view properties for new project members form
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayAdminMemberForm()
    {
        $project = $this->_data['project'];
        $page_title = $project->name." - "._LANG_ADMIN;
         
        $this->addData("page_title", $page_title);
        
        $pathway_url = "index.php?component=com_projects&action=get_admin&projectid=";
        $pathway_url .= $project->id;
        $this->getPathway()->addItem(_LANG_ADMIN, $pathway_url);
        $this->getPathway()->addItem(_LANG_PROJECTS_ADD_MEMBER);
        
        // Append page title to document title
        $this->getDocument()->appendTitle(" - ".$page_title);
    }
}
