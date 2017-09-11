<?php
class admin_mysql{
static $private='6';
static $cb='adm';

static function admin(){
	$r[]=['open','pop','admin_mysql,com|cb=1','','new'];
	return $r;}

static function injectJs(){return '';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#build
static function build($db){
	$cols='id,'.Sql::columns($db,3).',dateup';
	if($db)return Sql::read($cols,$db,'rr','');}

static function call($p){
	$cb=val($p,'cb');
	$db=val($p,'db');
	$r=self::build($db); if(!$r)return;
	array_unshift($r,array_keys(Sql::columns($db)));
	foreach($r as $k=>$v)foreach($v as $ka=>$va)$r[$k][$ka]=$va;
	return Build::table($r);}

static function com($p){
	$db=val($p,'db');
	$ret=inp('db',$db,32).' ';
	$ret.=popup('admin_mysql,call|cb=1|db','ok','btn');
	return $ret;}

#interface
static function content($p){
	$ret=self::com($p);
	$ret.=div(self::call($p));
	return $ret;}
}
?>