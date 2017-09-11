<?php

class proxy{
static $private='0';

static function admin(){
	$r[]=['com','pop','proxy,com','','com'];
	if(auth(6))$r[]=['com','pop','proxy,comim','','img'];
	if(auth(6))$r[]=['com','j','prx|proxy,deldr','','del'];
	return $r;}

static function protect($ret,$f){$fb=domain($f);
	$ret=str_replace('href="http://','href="###',$ret);
	$ret=str_replace('src="http://','src="###',$ret);
	$ret=str_replace('href="','href="http://'.$fb.'/',$ret);
	$ret=str_replace('src="','src="http://'.$fb.'/',$ret);
	$ret=str_replace('url(/"','url(http://'.$fb.'/',$ret);
	$ret=str_replace('href="###','href="http://',$ret);
	$ret=str_replace('src="###','src="http://',$ret);
	return $ret;}

static function get($p){
	$f=val($p,'url'); $f=http($f);
	if($f){
		if(is_img($f))$ret=img($f);
		else $ret=File::curl(http($f));}
		//$ret=self::protect($ret,$f);
	return $ret;}

static function delf($p){
	$f=val($p,'f');
	if($f)unlink($f);
	if($f.'.gz')unlink($f.'.gz');
	return 'del:'.$f;}

static function deldr($p){$ret='';
	$dr='usr/ifr/'; $f='usr/ifr'.date('ymd').'.tar';
	if(!is_file($f.'.gz') && !is_file($f))$ret.=Tar::buildFromDir($f,$dr);
	Dir::rmdir_r($dr);
	$ret.=aj('popup|proxy,delf|f='.$f,'x','btn');
	return $ret;}

static function getim($p){
	$u=val($p,'urim'); $ret='';
	$r=preg_split('/[()]/',$u); //p($r);
	list($min,$max)=explode('-',$r[1]);
	$l=strlen($min);
	$dr='usr/ifr/'; Dir::mkdir_r($dr);
	for($i=$min;$i<=$max;$i++){
		if($l==2){if($i<=9)$n='0'.$i; else $n=$i;}
		elseif($l==3){if($i<=9)$n='00'.$i; elseif($i<=99)$n='0'.$i; else $n=$i;}
		elseif($l==4){if($i<=9)$n='000'.$i; elseif($i<=999)$n='00'.$i; elseif($i<=99)$n='0'.$i; else $n=$i;}
		$f=$r[0].$n.$r[2]; $fa=$dr.after($f,'/');
		//if(fopen($f,'r'))
		$ok=@copy($f,$fa); //else echo $f.br();
		//if(!$ok){$d=@file_get_contents($f); if($d)$er=File::write($fa,$d);}
		$ret.=img('/'.$fa);}
	return $ret;}

static function com($p){
	$f=val($p,'url');
	$ret=input('url',$f,32).' ';
	$ret.=aj('prx|proxy,get||url','ok','btn');
	return $ret;}

static function comim($p){
	$f=val($p,'url');
	$ret=input('urim',$f,32).' ';
	$ret.=aj('prx|proxy,getim||urim','ok','btn');
	return $ret;}

static function content($p){$ret='';
	$f=val($p,'url');
	if($f)$ret=self::get($f);
return div($ret,'','prx');}
}
?>
