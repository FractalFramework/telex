<?php
class download{

static function admin(){
	$r[]=['','pop','Help,com|ref=download_app','','about'];
	return $r;}
	
static function createtar($f){
	$r=['prod','prog','fonts','cnfg/site.com.php','db/system','usr/tlex/','tar','amt.php','api.php','boot.php','call.php','htaccess.txt','index.php','favicon.ico','readme.txt','license.txt'];
	return Tar::buildFromList($f,$r);}

#content
static function content($prm){
	$f=val($prm,'fileName','fractalframework');
	$f.='.tar'; $fgz=$f.'.gz';
	if(is_file('/'.$fgz))unlink($fgz);
	$url=self::createtar($f);
	$ico=ico('download');
	return href('/'.$fgz,$ico.$url,'btn');
	return $ret;}
}
?>