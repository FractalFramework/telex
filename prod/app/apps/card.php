<?php

class card{
static $private='0';
static $a='card';
static $db='card';
static $cb='cdcbk';
static $cols=['title','function','name','site','infos'];
static $typs=['var','var','var','var','var'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function injectJs(){return '';}

static function headers(){
	Head::add('csscode','
	.cstitle{font-size:22px; font-family:Ubuntu;}
	.csfunction{font-size:18px; font-family:Ubuntu; font-color:silver;}
	.csname{font-size:14px; font-color:orange;}
	.cssite{font-size:14px; font-color:grey;}
	.csinfos{font-size:12px; font-color:silver;}
	.cscard{text-align:center; border:2px solid black; padding:20px 40px;}
	.cscard div{margin:4px;}');
	Head::add('jscode',self::injectJs());}

#operations
static function del($p){
	return appx::del($p);}
	
static function modif($p){
	return appx::modif($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}

static function form($p){
	return appx::form($p);}

#editor	
static function edit($p){
	return appx::edit($p);}
	
#reader
static function build($p){$id=val($p,'id');
	$colstr=implode(',',self::$cols);
	$r=Sql::read($colstr,self::$db,'ra',$id);
	return $r;}

static function stream($p){
	$p['t']='title';
	return appx::stream($p);}
	
#interfaces
static function tit($p){
	$p['t']='title';
	return appx::tit($p);}

//template
static function template(){
	return '[(card)
	[(title)*class=cstitle:div]
	[[(function)*class=csfunction:span] [(name)*class=csname:span]:div]
	[[(site)*(url):a]*class=cssite:div]
	[(infos)*class=csinfos:div]
*class=paneb cscard:div]';}
	
//call
static function call($p){
	$r=self::build($p);
	$r['card']=ico('vcard-o',32);
	$r['url']=http($r['site']);
	$r['site']=nohttp($r['site']);
	$template=self::template();
	$ret=Vue::read($r,$template);
	return $ret;}
	
//com (apps)
static function com($p){
	return appx::com($p);}
	
//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>