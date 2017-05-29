<?php
class upsql{
static $private=6;
static $server='tlex.fr';

static function call($p){
	$p=val($p,'app','');
	$f='http://'.self::$server.'/api.php?app=upsql&mth=render&p='.$p;
	$d=File::get($f);
	$r=json_dec($d);
	if($_SERVER['HTTP_HOST']!=self::$server)
		if(isset($r) && is_array($r)){
			Sql::insert2($p,$r,1,0);
			if($p=='lang')ses('Lang',Lang::com(ses('lng')));
			if($p=='icons')ses('Icon',Icon::com());
			return 'renove '.$p.' ok';}
		else return 'nothing'.Json::error();}

static function render($table){
	$keys=Sql::columns($table,1);
	if($table=='login')return;
	elseif($table=='desktop')$wh='where uid=1';
	elseif($table=='articles')$wh='where uid=1';
	else $wh='';
	$r=Sql::read($keys,$table,'rr',$wh,0);
	$ret=json_enc($r);
	return $ret;}

static function menu($p){//system tables
	$r=array('lang','icons','help','desktop','labels','conn','articles','sys','syslib','devnote');
	foreach($r as $k=>$v)
		if($v!='login')$ret[]=aj($p['rid'].'|upsql,call|app='.$v,$v,'btn');
	return implode('',$ret);}

static function content($p){
	$p['rid']=randid('md');
	$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
	$bt=hlpbt('upsql');
	$bt.=self::menu($p);
	return $bt.div('','',$p['rid']);}
}
?>