<?php
class upgrade{
static $private='6';
static $servr='tlex.fr';

static function injectJs(){return 'ajaxCall("popup|upgrade,auto","");';}
//static function headers(){Head::add('jscode',self::injectJs());}

static function auto(){
	$ret=update::loaddl();
	$r=['lang','icons','help','desktop'];
	foreach($r as $k=>$v){$ret.=apisql::call(['app'=>$v]); sleep(1);}
	return $ret;}

#interface
static function content($p){
	$local=file_get_contents('version.txt');
	$distant=file_get_contents('http://'.self::$servr.'/version.txt');
	if($distant>$local)Head::add('jscode',self::injectJs());
	ses('updated',1);}
}
?>