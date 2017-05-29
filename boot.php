<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
setlocale(LC_TIME,'fr_FR');
if(!isset($_SESSION['enc']) or !isset($_SESSION['connect']) or isset($_GET['reload'])){
	$f='cnfg/'.str_replace('www.','',$_SERVER['HTTP_HOST']).'.php'; include($f);
	$_SESSION['connect']=$f; $_SESSION['enc']=$utf8;
	$_SESSION['index']=$index; $_SESSION['updated']=0;
	if(isset($_COOKIE['lng']))$_SESSION['lng']=$_COOKIE['lng'];
	elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		$_SESSION['lng']=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
	else $_SESSION['lng']='fr';}
//lib
if(isset($_GET['dev']))$_SESSION['dev']=$_GET['dev']=='='?'prog':'prod';
if(!isset($_SESSION['dev']))$_SESSION['dev']='prod';
require($_SESSION['dev'].'/lib.php');
if(isset($_GET['logout'])){Auth::logout(); reload('/');}
if(!isset($_SESSION['time']))Auth::autolog();
$_SESSION['time']=time();
?>