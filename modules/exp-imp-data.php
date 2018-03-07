<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 05/03/2018
 * Time: 07:26 AM
 */

$user->allowAccess('exp-imp-data');
if($User['roleId']!=1)
     header('Location: '.WEB_ROOT);
