<?php
#ph1.fr license GNU/GPL
session_start();
require('boot.php');
$app=get('appName');
$mth=get('appMethod');
$p=_jrb(get('params'),1);
$p['appName']=$app;
$p['appMethod']=$mth;
if(isset($p['verbose']))pr($p);
#request
$content=App::open($app,$p);
//$a=new $app; $content=$a->$mth($p);
#render
$ret=Head::build();
if(get('popup'))$ret.=Build::popup($content,$p);
elseif(get('pagup'))$ret.=Build::pagup($content,$p);
elseif(get('imgup'))$ret.=Build::imgup($content);
elseif(get('bubble'))$ret.=Build::bubble($content);
elseif(get('menu'))$ret.=Build::menu($content);
elseif(get('drop'))$ret.=Build::menu($content);
elseif(get('ses'))sez($p['k'],$p['v']);
else $ret.=$content;
echo encode($ret);
?>