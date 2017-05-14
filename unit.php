<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');

class String
{
    //contains the internal data
    var $data;

    // constructor
    function String($data) {
        $this->data = $data;
    }

    // creates a deep copy of the string object
    function copy() {
    }

    // adds another string object to this class
    function add($string) {
    }

    // returns the formated string
    function toString($format) {
    }
}
?>