<?php

class tabler{
static $private='0';
static $a='tabler';
static $db='tabler';
static $cb='tbl';
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

static function titles($p){
	return appx::titles($p);}

static function injectJs(){return '';}

static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

//trans
static function trans($d){
	$ret=Trans::call(['txt'=>$d]);
	if(strpos($ret,':table]'))$ret=substr($ret,1,-7);
	return $ret;}

#edit
static function form($p){$cb=self::$cb; $ret='';
	$r=vals($p,self::$cols); $uid=val($p,'uid');
	foreach($r as $k=>$v){
		if($k=='txt'){$v=Conn::mktable($v);
			$ret.=div($v,'editarea',$k,'',['contenteditable'=>'true']);}
		elseif($k=='pub')$ret.=div(appx::pub($k,$v,$uid));
		elseif($k=='tit')$ret.=div(input($k,$v?$v:date('ymdhi'),'',lang('title')));}
	return $ret;}

static function del($p){return appx::del($p);}

static function save($p){
	$a=self::$a; $db=self::$db; $cb=self::$cb;
	$r=[ses('uid')]; foreach(self::$cols as $v){
		if($v=='txt')$r[]=self::trans(val($p,$v));
		else $r[]=val($p,$v);}
	$p['id']=Sql::insert($db,$r);
	return $a::edit($p);}

static function create($p){
	return appx::create($p);}

static function modif($p){$id=val($p,'id');
	$a=self::$a; $db=self::$db;
	$r=vals($p,self::$cols);
	$r['txt']=self::trans($r['txt']);
	Sql::updates($db,$r,$id);
	return $a::edit($p);}

static function edit($p){return appx::edit($p);}

#build
static function build($p){return appx::build($p);}

static function play($p){
	$p['conn']=0;
	$r=self::build($p);
	$ret=div($r['tit'],'tit');
	$ret.=div(Conn::mktable($r['txt']),'txt');
	return $ret;}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (read)
static function call($p){
	return div(self::play($p),'','play'.$p['id']);}

//com (write)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>