<?php
//fractalframework@2017
//http://opensource.org/licenses/gpl-license.php
session_start();
require('boot.php');
chrono('');
$app=get('app');
if(!$app)$app=$_SESSION['index'];
sez('app',$app);
#/app/model/p1:v1,p2:v2
$p=_jrb(get('params'),1);
sez('applng',$app);
Lang::$app=$app;
require($_SESSION['dev'].'/index.php');
Sql::close();
?>