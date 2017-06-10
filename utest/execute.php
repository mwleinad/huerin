<?php

	define('DOC_ROOT', '/var/www/html/huerin_test');
	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');

require_once DOC_ROOT.'/utest/unit.php';
require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/TextUI/TestRunner.php';

$suite  = new PHPUnit_Framework_TestSuite();
$suite->addTest(new StringTest("testAdd"));
$result = PHPUnit::run($suite);

echo $result -> toString();
?>