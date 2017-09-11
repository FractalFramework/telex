<?php
session_start();
require('boot.php');
///api/App/Mth/a:1,b:2
if($app=get('app')){$mth=get('mth'); $p=get('prm'); 
	if(is_numeric($p))$prm['id']=$p; elseif($p)$prm=_jrb($p,1); else $prm=get('p');
	if(method_exists($app,$mth))echo $app::$mth($prm);}
//api.php?oAuth=xxx&msg=hello
if($oAuth=get('oAuth'))echo tlxcall::post(['oAuth'=>$oAuth]);
?>