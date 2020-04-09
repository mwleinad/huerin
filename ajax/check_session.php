<?php
include_once('../init.php');
include_once('../config.php');
session_start();
echo isset($_SESSION["User"]) ? 'ok' : 'fail';