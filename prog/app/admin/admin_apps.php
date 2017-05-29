<?php
class admin_apps{
static $private='6';
	
//content
static function content($p){$ret='';
$r[]=array('App','/','privacy');
$r=Dir::read(ses('dev').'/app');
if(is_array($r))foreach($r as $dir=>$files){
	if(is_array($files) && $dir)foreach($files as $k=>$file)if(!is_array($file)){
	$app=before($file,'.');
	$lk=href('/app/'.$app,$app);
	$private=isset($app::$private)?$app::$private:0;
	$r[]=array($lk,$dir,$private);}}
return Build::table($r);}
}
?>