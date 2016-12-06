<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
setlocale(LC_TIME,'fr_FR');
if(!isset($_SESSION['enc']) or !isset($_SESSION['index']) or isset($_GET['reload'])){
	$f='cnfg/'.str_replace('www.','',$_SERVER['HTTP_HOST']).'.txt'; $d='';
	if(is_file($f))$d=file_get_contents($f);
	if($d)$r=explode(',',$d);
	$_SESSION['enc']=isset($r[0])?$r[0]:1;//encoding
	$_SESSION['index']=isset($r[1])?$r[1]:'index';}
//lib
if(isset($_GET['dev']))$_SESSION['dev']=$_GET['dev']=='='?'prog':'prod';
if(!isset($_SESSION['dev']))$_SESSION['dev']='prod';
require($_SESSION['dev'].'/lib.php');
if(isset($_GET['reboot'])){$_SESSION=''; reload('/');}
if(!isset($_SESSION['time']))Auth::autolog();
$_SESSION['time']=time();
?>