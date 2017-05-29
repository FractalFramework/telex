<?php

class purpose{
static $private='0';
static $a='purpose';
static $db='purpose';
static $cb='mdl';
static $cols=['en','fr','es','pub'];
static $typs=['text','text','text','int'];
static $lang='fr';

function __construct(){
	$r=['a','db','cb','cols'];
	self::$lang=ses('lng');
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){//var,text,int
	$r=array_combine(self::$cols,self::$typs);
	appx::install($r);}

static function admin($rid=''){
	$p['rid']=$rid;
	$p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function form($p){return appx::form($p);}
static function del($p){return appx::del($p);}
static function save($p){return appx::save($p);}
static function create($p){$p['pub']=2; return appx::create($p);}
static function modif($p){return appx::modif($p);}
static function edit($p){return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

#play
static function template(){
	return '[[(en)*class=cell:div][(fr)*class=cell:div][(es)*class=cell:div]*class=row:div]';}

static function play($p){
	$id=val($p,'id'); $re=val($p,'redo'); $ret=''; $va='';
	$r=self::build($p);
	foreach(self::$cols as $k=>$v)if($r[$v] && $v!='pub')$va=$v;
	foreach(self::$cols as $k=>$v)if($v!='pub'){
		if((!$r[$v] && $va) or $re){
			$r[$v]=Yandex::com(['from'=>$va,'to'=>$v,'txt'=>$r[$va]],1);
			Sql::update(self::$db,$v,$r[$v],$id);}
		$ret.=div($r[$v],'board',$v.$id);}
	return $ret;}

static function stream($p){
	$p['t']=self::$lang;
	return appx::stream($p);}

#call (read)
static function tit($p){
	$p['t']=self::$lang;
	return appx::tit($p);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

#com (edit)
static function com($p){return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>