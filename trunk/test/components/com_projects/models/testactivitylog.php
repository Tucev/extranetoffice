<?php
/**
 * @version 	$Id$
 * @package		ExtranetOffice
 * @copyright	Copyright (C) 2009 E-noise.com Limited. All rights reserved.
 * @license		BSD revised. See LICENSE.
 */

require_once 'activitylog.php';

class testactivitylog extends PHPUnit_TestCase
{
    // contains the object handle of the tested class
    var $activitylog;

    // constructor of the test suite
    function testactivitylog($name) {
       $this->PHPUnit_TestCase($name);
    }

    // called before the test functions will be executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function setUp() {
        // create a new instance of activitylog
        $this->activitylog = new activitylog();
    }

    // called after the test functions are executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function tearDown() {
        // delete your instance
        unset($this->activitylog);
    }

    // test the getActivityLog function
    function testGetActivityLog() {
        $result = $this->activitylog->getActivityLog();
        $expected = ;
        $this->assertTrue($result == $expected);
    }

    // test the saveActivityLog function
    function testSaveActivityLog() {
      $this->activitylog->saveActivityLog($projectid, $userid, $type, $action, $title, $description, $url, $assignees, $notify);
    }

    // test the notify function
    function test_notify() {
        $result = $this->activitylog->_notify();
        $this->assertTrue($result);
    }
  }
?>