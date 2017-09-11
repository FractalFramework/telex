<?php

class sticker{
static $private='0';
static $a='sticker';
static $db='sticker';
static $cb='stc';
static $cols=['txt','clr','bkg'];
static $typs=['var','var','var'];
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
	Head::add('csscode','.sticker{font-family:Lato-Black; font-size:32px; text-align:center; padding:100px 20px; background-size:cover; background-position:center center; min-height:240px;}
	.stickin{vertical-align:middle;}');
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
	$p['help']='sticker_edit';
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function play($p){
	$r=self::build($p);
	//$clr=Clr::read($r['clr']);
	$s='color:#'.$r['clr'].'; ';
	$im=Build::thumb($r['bkg'],'full');
	if($im)$s.='background-image:url(/'.$im.')';
	else $s.='background-image:linear-gradient(45deg, rgb(248, 240, 35) 0%, rgb(5, 174, 53) 100%);';
	$ret=div($r['txt'],'stickin');
	return div($ret,'sticker','',$s);}

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
	self::install();
	return appx::content($p);}
}
?>