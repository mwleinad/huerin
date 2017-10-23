<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */


function smarty_modifier_number($number)
{
    if(!(string)$number){
        return null;
    }
    $number = floatval((string)$number);


    return "$".number_format($number, 2, '.', ',');
}

?>
