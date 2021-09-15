<?php
include_once('../init.php');
include_once('../config.php');
echo isset($_SESSION["User"]) ? 'ok' : 'fail';
