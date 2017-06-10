<?php

	define('DOC_ROOT', '/var/www/html/huerin_test');
	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');

require_once DOC_ROOT.'/unit.php';
require_once 'PHPUnit/Autoload.php';

class StringTest extends PHPUnit_Framework_TestCase
{
    // contains the object handle of the string class
    var $abc;

		public function __call($command, $arguments) {
        $class_methods = get_class_methods(__CLASS__);
        if(!in_array($command, $class_methods)) {
            throw new BadMethodCallException(
                  "Method $command not defined."
                );
        }
    }
    // constructor of the test suite
    function StringTest($name) {
				$this->PHPUnit_Framework_TestCase($name);
    }

    // called before the test functions will be executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function setUp() {
        // create a new instance of String with the
        // string 'abc'
        $this->abc = new String("abc");
    }

    // called after the test functions are executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function tearDown() {
        // delete your instance
        unset($this->abc);
    }

    // test the toString function
    function testToString() {
        $result = $this->abc->toString('contains %s');
        $expected = 'contains abc';
        $this->assertTrue($result == $expected);
    }

    // test the copy function
    function testCopy() {
      $abc2 = $this->abc->copy();
      $this->assertEquals($abc2, $this->abc);
    }

    // test the add function
    function testAdd() {
        $abc2 = new String('123');
        $this->abc->add($abc2);
        $result = $this->abc->toString("%s");
        $expected = "abc123";
        $this->assertTrue($result == $expected);
    }
  }
?>