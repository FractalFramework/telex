<?php
class download{
	
	static function createtar($f){
		$r=['prog','fonts','cfng/site.com.php','amt.php','api.php','boot.php','call.php','htaccess.txt','index.php','favicon.ico','readme.txt'];
		return Tar::buildFromList($f,$r);}

	#content
	static function content($prm){
		$f=val($prm,'fileName','fractalframework');
		$f.='.tar'; $fgz=$f.'.gz';
		if(is_file($fgz))unlink($fgz);
		$url=self::createtar($f);
		$ico=ico('download');
		return href('/'.$fgz,$ico.$url,'btn');
	return $ret;}
}
?>