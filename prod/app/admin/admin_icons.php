<?php

class admin_icons{
static $private='6';
static $db='icons';

static function headers(){
	Head::add('csscode','');}

//install
static function install(){
	Sql::create(self::$db,['ref'=>'var','icon'=>'var']);}

//save
static function update($p){$rid=$p['rid'];
	Sql::update(self::$db,'icon',$p[$rid],$p['id']);
	$r=sesclass('Icon','com','',1);
	return self::com($p);}

static function del($p){
	$nid=Sql::delete(self::$db,$p['id']);
	return self::com($p);}

static function save($p){//$lang=val($p,'lang');,$lang
	$nid=Sql::insert(self::$db,array($p['ref'],$p['icon']));
	$r=sesclass('Icon','com','',1);
	return self::com($p);}

static function edit($p){$rid=randid('icons');//id
	$r=Sql::read('ref,icon',self::$db,'ra','where id='.$p['id']);
	$ret=label($rid,$r['ref']);
	$ret.=goodinput($rid,$r['icon']);
	$ret.=aj('admm,,x|admin_icons,update|id='.$p['id'].',rid='.$rid.'|'.$rid,lang('save'),'btsav');
	$ret.=aj('admm,,x|admin_icons,del|id='.$p['id'],lang('del'),'btdel');
	$ret.=aj('popup|fontawesome',pic('pictos'),'btn');
	return $ret;}

static function open($p){$ref=val($p,'ref');
	$p['id']=Sql::read('id',self::$db,'v',['ref'=>$ref]);
	if(!$p['id'])$p['id']=Sql::insert(self::$db,[$ref,'']);
	if($p['id'])return self::edit($p);}

static function add($p){//ref,icon
	$ref=val($p,'ref'); $icon=val($p,'icon');
	$ret=inp('ref',$ref?$ref:'',16,'ref').inp('icon',$icon?$icon:'',16,'icon');
	$ret.=aj('admm,,x|admin_icons,save||ref,icon',lang('save'),'btsav');
	return $ret;}

//table
static function select(){$ret='';
	if(ses('auth')>6){
		$ret.=aj('popup|admin_icons,add',langp('add'),'btn');
		$ret.=aj('popup|Sql,mkbcp|b=icons',langp('backup'),'btsav');
		if(Sql::exists('icons_bak'))
		$ret.=aj('popup|Sql,rsbcp|b=lang',langp('restore'),'btdel');}
		$ret.=aj('popup|fontawesome',pic('pictos'),'btn');
		$ret.=aj('admm|admin_icons',pic('reload'),'btn').br();
	return $ret;}

static function com(){$rb=array();
	$bt=self::select().br();
	$r=Sql::read('id,ref,icon',self::$db,'','order by ref');
	if($r)foreach($r as $k=>$v){
		$ref=aj('popup|admin_icons,edit|id='.$v[0],$v[1],'btn');
		$icon=ico($v[2],32);
		if(!$v[2])$rc[$k]=array($ref,$icon); 
		else $rb[$k]=array($ref,$icon.' '.$v[2]);}
	if(isset($rc))$rb=array_merge($rc,$rb);
	array_unshift($rb,array('ref','icon'));
	return $bt.Build::table($rb,'','',1);}

//content
static function content($p){$ret='';
	//self::install();
	$ret=self::com();
	return div($ret,'','admm');}

}

?>