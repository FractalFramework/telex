<?php
class model1{
static $private='0';
static $a='model1';
static $db='model1';
static $cb='mdl';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

static function modif($p){
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function edit($p){
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function template(){
	return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){
	//$r=self::build($p);
	return appx::play($p);}

static function stream($p){
	return appx::stream($p);}

#call (read)
static function tit($p){
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>