<?php
class PHPFrame_Installer
{
    /**
     * App Config object
     * 
     * @var PHPFrame_Config
     */
    private $_config=null;
    /**
     * Config object containing sources
     * 
     * @var PHPFrame_Config
     */
    private $_sources=null;
    /**
     * PHPFrame_SCM object
     * 
     * @var PHPFrame_SCM
     */
    private $_scm=null;
    /**
     * Path to installation
     * 
     * @var string
     */
    private $_install_path=null;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_config = new PHPFrame_Config();
        
        
        $this->_sources = new PHPFrame_Config();
    }
    
    public function install()
    {
        
    }
    
    public function update()
    {
        
    }
    
    public function remove()
    {
        
    }
}