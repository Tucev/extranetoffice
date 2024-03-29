<?php
/**
 * src/components/com_projects/views/people/view.php
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
 * projectsViewPeople Class
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
class projectsViewPeople extends PHPFrame_MVC_View
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
        parent::__construct('people', $layout);
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
            
            // Add url to project home
            $project_url = "index.php?component=com_projects&action=get_project_detail";
            $project_url .= "&projectid=".$project->id;
            $project_url = PHPFrame_Utils_Rewrite::rewriteURL($project_url);
            $this->addData("project_url", $project_url);
        }
        
        $this->addData('page_title', $project->name);
        
        // Set tool data
        $this->_data['tool'] = $this->getName();
        $tool_url = 'index.php?component=com_projects&action=get_people';
        $tool_url .= '&projectid='.$this->_data['project']->id;
        $this->_data['tool_url'] = PHPFrame_Utils_Rewrite::rewriteURL($tool_url);
        
        $this->getDocument()->appendTitle(" - ".$project->name." - "._LANG_PEOPLE);
        
        parent::display();
    }
    
    /**
     * Custom display method triggered by list layout.
     * 
     * @access protected
     * @return void
     * @since  1.0
     */
    protected function displayPeopleList()
    {
        $project = $this->_data['project'];
        
        $this->getPathway()->addItem(ucfirst($this->getName()));
    }

}
