<?php
error_reporting(0);

include 'FileMaker.php';

define('FMWPE_HOST',"localhost");
define('FMWPE_USER',"php");
define('FMWPE_PASS',"php");
define("FMDB_WEB","demo");

$fm=new FileMaker(FMDB_WEB,FMWPE_HOST,FMWPE_USER,FMWPE_PASS);
?>