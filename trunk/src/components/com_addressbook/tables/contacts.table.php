<?php
/**
 * @version     $Id$
 * @package        ExtranetOffice
 * @subpackage    com_addressbook
 * @copyright    Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license        BSD revised. See LICENSE.
 */

/**
 * addressbookTableContacts Class
 * 
 * @package        ExtranetOffice
 * @subpackage     com_addressbook
 * @since         1.0
 */
class addressbookTableContacts extends PHPFrame_Database_Table {
    /**
     * The issue id
     * 
     * int(11) auto_increment
     * 
     * @var int
     */
    var $id=null;
    /**
     * User ID of issue creator
     * 
     * int(11)
     * 
     * @var int
     */
    var $created_by=null;
    /**
     * Date created
     * 
     * datetime
     * 
     * @var string
     */
    var $created=null;
    /**
     * User ID
     * 
     * int(11)
     * 
     * @var int
     */
    var $userid=null;
    /**
     * Access level
     * 
     * enum('0','1') 0=Public, 1=Private, only owner and assigned users
     * 
     * @var int
     */
    var $access=null;
    /**
     * First / given name
     * 
     * varchar(50)
     * 
     * @var string
     */
    var $given=null;
    /**
     * varchar(50) 
     * @var string
     */
    var $family=null;
    /**
     * varchar(100)
     * @var string
     */
    var $fn=null;
    /**
     * varchar(50)
     * @var string
     */
    var $nickname=null;
    /**
     * varchar(50)
     * @var string
     */
    var $category=null;
    /**
     * varchar(255)
     * @var string
     */
    var $home_email=null;
    /**
     * varchar(255)
     * @var string
     */
    var $work_email=null;
    /**
     * varchar(255)
     * @var string
     */
    var $other_email=null;
    /**
     * varchar(100)
     * @var string
     */
    var $company_name=null;
    /**
     * varchar(30)
     * @var string
     */
    var $job_title=null;
    /**
     * varchar(20)
     * @var string
     */
    var $home_phone=null;
    /**
     * varchar(20)
     * @var string
     */
    var $work_phone=null;
    /**
     * varchar(20)
     * @var string
     */
    var $cell_phone=null;
    /**
     * varchar(20)
     * @var string
     */
    var $fax=null;
    /**
     * text
     * @var string
     */
    var $note=null;
    /**
     * varchar(100)
     * @var string
     */
    var $website=null;
    /**
     * varchar(50)
     * @var string
     */
    var $home_street=null;
    /**
     * varchar(50)
     * @var string
     */
    var $home_extended=null;
    /**
     * varchar(30)
     * @var string
     */
    var $home_locality=null;
    /**
     * varchar(30)
     * @var string
     */
    var $home_region=null;
    /**
     * varchar(15)
     * @var string
     */
    var $home_postcode=null;
    /**
     * varchar(30)
     * @var string
     */
    var $home_country=null;
    /**
     * varchar(50)
     * @var string
     */
    var $work_street=null;
    /**
     * varchar(50)
     * @var string
     */
    var $work_extended=null;
    /**
     * varchar(30)
     * @var string
     */
    var $work_locality=null;
    /**
     * varchar(30)
     * @var string
     */
    var $work_region=null;
    /**
     * varchar(15)
     * @var string
     */
    var $work_postcode=null;
    /**
     * varchar(30)
     * @var string
     */
    var $work_country=null;
    /**
     * varchar(50)
     * @var string
     */
    var $other_street=null;
    /**
     * varchar(50)
     * @var string
     */
    var $other_extended=null;
    /**
     * varchar(30)
     * @var string
     */
    var $other_locality=null;
    /**
     * varchar(30)
     * @var string
     */
    var $other_region=null;
    /**
     * varchar(15)
     * @var string
     */
    var $other_postcode=null;
    /**
     * varchar(30)
     * @var string
     */
    var $other_country=null;
    
    /**
     * Construct
     * 
     * @return    void
     * @since    1.0
     */
    function __construct() {
        parent::__construct( '#__contacts', 'id' );
    }
}
?>