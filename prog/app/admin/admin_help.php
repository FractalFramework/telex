<?php

class admin_help{
static $private='6';
static $db='help';
static $ad='admin_help';

//install
static function install(){
	Sql::create(self::$db,['ref'=>'var','txt'=>'text','lang'=>'var'],1);}

//create lang
static function create($p){
	$newlng=val($p,'newlng'); $lng='fr';
	$ret=inp('newlng',$newlng);
	$ret.=aj('admcnn|'.self::$ad.',create||newlng',langp('add language'),'btn');
	if($newlng){
		$r=Sql::read('ref,txt',self::$db,'rr','where lang="'.$lng.'" limit 80,10'); //p($r);
		foreach($r as $k=>$v){
			$ex=Sql::read('txt',self::$db,'v','where ref="'.$v['ref'].'" and lang="'.$newlng.'"');
			if(!$ex){
				$res=Yandex::com(['from'=>$lng,'to'=>$newlng,'txt'=>$v['txt']]);
				$v['txt']=utf8_decode($res); $v['lang']=$newlng;
				Sql::insert(self::$db,$v); $r[$k]=$v;}
			else $r[$k]['txt']=$ex;}
	$ret.=Build::table($r);}
	return $ret;}

//tools
static function goodid($p){
	return Sql::read('id',self::$db,'v',['ref'=>$p['ref'],'lang'=>$p['lang']]);}

static function insertup($p){$id=self::goodid($p);
	if($id)Sql::update(self::$db,'txt',$p['txt'],$id);
	else Sql::insert(self::$db,[$p['ref'],$p['txt'],$p['lang']]);}

static function translate($p){$voc=''; $txt=''; $copy=val($p,'copy');
	$r=Sql::read('lang,txt',self::$db,'kv',['ref'=>$p['ref']]);
	foreach($r as $k=>$v){
		if($p['lang']!='en' && isset($r['en'])){$from='en'; $txt=$r['en'];}
		if($p['lang']!='fr' && isset($r['fr'])){$from='fr'; $txt=$r['fr'];}}
	if($copy)$voc=utf8_decode(html_entity_decode($txt));
	elseif($txt)$voc=Yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt],1);
	//if($copy){$voc=utf8_decode($txt); Sql::update(self::$db,'txt',$txt,$copy);}
	//elseif($voc)self::insertup(['ref'=>$p['ref'],'txt'=>$txt,'lang'=>$p['lang']]);
	return $voc;}

static function equalize($p){
	$r=Sql::read('ref,lang,txt',self::$db,'kkv','');
	$rb=array_keys($r);
	foreach($rb as $k=>$v)
		if(!isset($r[$v][$p['lang']])){$txt=''; $voc='';
			if($p['lang']!='en' && isset($r[$v]['en'])){$from='en'; $txt=$r[$v]['en'];}
			if($p['lang']!='fr' && isset($r[$v]['fr'])){$from='fr'; $txt=$r[$v]['fr'];}
			//if($txt)$voc=Yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt],1);
			self::insertup(['ref'=>$v,'txt'=>$voc,'lang'=>$p['lang']]);}
	return self::com($p);}

//save
static function update($p){$rid=$p['rid'];
	Sql::update(self::$db,'txt',val($p,$rid),$p['id']);
	return self::com($p);}

static function del($p){
	$nid=Sql::delete(self::$db,$p['id']);
	return self::com($p);}

static function save($p){
	$nid=Sql::insert(self::$db,array($p['ref'],$p['txt'],$p['lang']));
	return self::com($p);}

static function addfrom($p){
	$p['voc']=Yandex::com(['from'=>$p['from'],'to'=>$p['lang'],'txt'=>$p['fvoc']],1);
	$p['id']=Sql::insert(self::$db,array($p['ref'],$p['voc'],$p['lang']));
	return self::edit($p);}

static function edit($p){$rid=randid('ref');
	$to=val($p,'to')?'socket,,x':'admcnn,,x';
	$r=Sql::read('ref,txt,lang',self::$db,'ra','where id='.$p['id']);
	$ret=label($rid,$r['ref'].' ('.$r['lang'].')');
	$ret.=aj($to.'|'.self::$ad.',update|id='.$p['id'].',rid='.$rid.',lang='.$r['lang'].'|'.$rid,lang('save'),'btsav');
	$ret.=aj($to.'|'.self::$ad.',del|id='.$p['id'].',lang='.$r['lang'],lang('del'),'btdel');
	$ret.=aj('input,'.$rid.'|'.self::$ad.',translate|ref='.$r['ref'].',lang='.$r['lang'].',copy='.$p['id'],pic('copy'),'btn');
	$ret.=aj('input,'.$rid.'|'.self::$ad.',translate|ref='.$r['ref'].',lang='.$r['lang'],pic('language'),'btn');
	$lgb=$r['lang']=='fr'?'en':'fr';
	$ret.=aj('popup,,x|'.self::$ad.'|lang='.$lgb,ico('window-maximize'),'btn');
	foreach(Lang::$langs as $v)if($v!=$r['lang']){
		$id=Sql::read('id',self::$db,'v',['ref'=>$r['ref'],'lang'=>$v]);
		if($id)$ret.=aj('popup|'.self::$ad.',edit|id='.$id,$v,'btn');
		else $ret.=aj('popup|'.self::$ad.',addfrom|lang='.$v.',ref='.$r['ref'].',from='.$r['lang'].',fvoc='.$r['txt'],$v,'btsav');}
	$ret.=br().textarea($rid,$r['txt'],40,4);
	return $ret;}

static function open($p){$ref=val($p,'ref');
	$p['id']=Sql::read('id',self::$db,'v',['ref'=>$ref]);
	if(!$p['id'])$p['id']=Sql::insert(self::$db,[$ref,'',ses('lng')]);
	if($p['id'])return self::edit($p);}

static function add($p){//ref,txt
	$ref=val($p,'ref'); $txt=val($p,'txt');
	$ret=inp('ref',$ref?$ref:'',16,'ref').textarea('txt',$txt?$txt:'',40,4,'ref');
	$ret.=aj('admcnn,,x|'.self::$ad.',save||lang,ref,txt',lang('save'),'btsav');
	return $ret;}

//table
static function select($lang){
	$ret=hidden('lang',$lang);
	//$r=Sql::read('distinct(lang)',self::$db,'rv','');
	$r=Lang::$langs;
	foreach($r as $v){$c=$v==$lang?' active':'';
		$rc[]=aj('admcnn|'.self::$ad.',com|lang='.$v,$v,'btn'.$c);}
	$ret.=div(implode('',$rc),'pane').br();
	if(ses('auth')>6){
		$ret.=aj('popup|'.self::$ad.',add',langp('add'),'btn');
		$ret.=aj('admcnn|'.self::$ad.',equalize||lang',langp('equalize'),'btn');
		$ret.=aj('popup|Sql,mkbcp|b=help',langp('backup'),'btsav');
		if(Sql::exists('help_bak'))
		$ret.=aj('popup|Sql,rsbcp|b=lang',langp('restore'),'btdel');
		$ret.=aj('admcnn|'.self::$ad.',create',langp('add language'),'btn').br();}
	return $ret;}

static function com($p){$rb=array();
	$lang=val($p,'lang');
	$bt=self::select($lang).br();
	$r=Sql::read('id,ref,txt',self::$db,'','where lang="'.$lang.'"');
	$n=count($r);
	$bt.=span($n.' '.langs('occurence',$n,1),'small');
	if($r)foreach($r as $k=>$v){$v[2]=nl2br($v[2]);
		if(ses('auth')>6)$ref=aj('popup|'.self::$ad.',edit|id='.$v[0],$v[1],'btn');
		else $ref=$v[1];
		if($v[2])$rb[$k]=array($ref,$v[2]);
		else $rc[$k]=array($ref,$v[2]);}
	if(isset($rc))$rb=merge($rc,$rb);
	array_unshift($rb,array('ref',$lang));
	return $bt.Build::table($rb,'','',1);}

//content
static function content($p){$ret='';
	self::install();
	$lang=val($p,'lang',Lang::$lang);
	$ret=self::com(array('lang'=>$lang));
	return div($ret,'','admcnn');}

}

?>