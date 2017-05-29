<?php

class urgency{
static $private='0';
static $a='urgency';
static $db='urgency';
static $cb='smcbk';
static $cols=['txt'];
static $typs=['var'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(['txt'=>'var']);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function injectJs(){return '';}

static function headers(){
	Head::add('csscode','.urgency{font-family:Lato-Black; font-size:44px; text-align:center; border:10px solid black; padding:20px;}');
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
static function build($p){
	return appx::build($p);}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (connectors)
static function call($p){
	$p['conn']=0;
	$ret=self::build($p);
	return div($ret['txt'],'urgency');}

//com (apps)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>