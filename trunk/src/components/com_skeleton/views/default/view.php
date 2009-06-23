<?php
/**
 * @version     $Id$
 * @package        PHPFrame
 * @subpackage    com_skeleton
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * skeletonViewDefault Class
 * 
 * @package        PHPFrame
 * @subpackage     com_skeleton
 * @author         Luis Montero [e-noise.com]
 * @since         1.0
 * @see         PHPFrame_MVC_View
 */
class skeletonViewDefault extends PHPFrame_MVC_View {
    /**
     * Constructor
     * 
     * @return     void
     * @since    1.0
     */
    function __construct($layout) {
        $document = PHPFrame::Response()->getDocument();
        $document->addScript("lib/jquery/plugins/lightbox/jquery.lightbox-0.5.js");
        
        // Invoke the parent to set the view name and default layout
        parent::__construct('default', $layout);
    }
}
