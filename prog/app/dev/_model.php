<?php

class _model{	
static $private='0';
static $db='_model';
static $a='_model';

/*static function install(){
	Sql::create(self::$db,['tit'=>'var','txt'=>'var'],0);}*/

static function admin(){
	$r[]=['','j','popup|'.self::$a.',content','plus',lang('open')];
	$r[]=['','pop','Help,com|ref='.$a.'_app','help','-'];
	if(auth(4))$r[]=['admin','j','pagup|dev,seeCode|appSee='.$a,'code','Code'];
	return $r;}

static function injectJs(){
	return '';}

static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

static function titles($p){
	$d=val($p,'appMethod');
	$r['content']='welcome';
	$r['build']='model';
	if(isset($r[$d]))return lang($r[$d]);}

#build
/*static function build($p){$id=val($p,'id');
	$r=Sql::read('all',self::$db,'ra',$id);
	return $r;}*/

#read
static function call($p){
	return $p['msg'].': '.$p['inp1'];}

static function com(){
	return self::content($p);}

#content
static function content($p){
	//self::install();
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('inp1','value1','','1');
	$ret.=aj('popup|'.self::$a.',call|msg=text|inp1',lang('send'),'btn');
return div($ret,'pane');}
}
?>