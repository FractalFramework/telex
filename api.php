<?php
session_start();
require('boot.php');
$app=get('app');
///api/App/Mth/a:1,b:2
if($app){$mth=get('mth'); $prm=get('prm')?_jrb(get('prm'),1):get('p');
	if(method_exists($app,$mth))echo $app::$mth($prm);}
?>