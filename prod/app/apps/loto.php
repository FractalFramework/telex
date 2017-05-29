<?php

class loto{
static $private='0';
static $a='loto';
static $db='loto';
static $cb='lto';
static $cols=['tit','nb','result','day'];
static $typs=['var','int','int','date'];
static $db2='loto_vals';//second db
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create(self::$db2,['bid'=>'int','uid'=>'int','val'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '
	
	';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){return appx::collect($p);}
static function del($p){$p['db2']=self::$db2; return appx::del($p);}
static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}
static function form($p){return appx::form($p);}
static function create($p){return appx::create($p);}
static function edit($p){$p['collect']=self::$db2; return appx::edit($p);}

#build
static function winner($id,$res){
	$r=Sql::read_inner('name',self::$db2,'login','uid','rv',['bid'=>$id,'val'=>$res]);
	if($r)return lang('winner').' : '.implode(', ',$r);
	else return lang('no winner');}

static function draw($r,$id){
	for($i=0;$i<$r['nb'];$i++)$rv[]=rand(0,9); $val=implode('',$rv);
	Sql::update(self::$db,'result',$val,$id);
	return $val;}

static function validate($p){
	$id=val($p,'id'); $val=val($p,'val'.$id); $nb=val($p,'nb');
	Sql::insert(self::$db2,['bid'=>$id,'uid'=>ses('uid'),'val'=>$val]);
	return lang('thank you');}

static function balls($id,$n,$v){$ret='';
	//$d='10102 10103 10104 10105 10106 10107 10108 10109 10110 10111';
	//$d='65296 65297 65298 65299 65300 65301 65302 65303 65304 65305';
	//$ra=explode(' ',$d);
	for($i=0;$i<10;$i++){$c=$v==$i?' btok':'';
		$j='loto'.$id.'|'.self::$a.',game|id='.$id.',n='.$n.',v='.$i.'|val'.$id;
		$ret.=aj($j,$i,'btsav'.$c);}
	return div($ret);}

static function game($p){$ret='';
	$id=val($p,'id'); $n=val($p,'n',0); $v=val($p,'v',0);
	$nb=Sql::read('nb',self::$db,'v',$id); $val=val($p,'val'.$id,str_pad('',$nb,'0'));
	$rv=str_split($val); $rv[$n]=$v; $val=implode('',$rv);
	for($i=0;$i<$nb;$i++)$ret.=self::balls($id,$i,$rv[$i]);
	$ret.=aj('loto'.$id.'|'.self::$a.',validate|id='.$id.'|val'.$id,langp('play it'),'btsav');
	$ret.=hidden('val'.$id,$val);
	return $ret;}

static function results($r,$id){
	$val=Sql::read('val',self::$db2,'v',$id);
	if($val && $val==$r['result'])$ret=help('loto_win');
	else $ret=help('loto_loose','panec');
	return $ret;}

#play
static function build($p){return appx::build($p);}

static function play($p){
	$id=val($p,'id');
	$r=self::build($p);
	$ret=div($r['tit'],'tit');
	$maxday=$r['day']; $end=strtotime($maxday); 
	if($end>ses('time'))$current=1; else $current=0;
	if(!$current && !$r['result'])$val=self::draw($r,$id);
	$ex=Sql::read('id',self::$db2,'v',['bid'=>$id,'uid'=>ses('uid')]);
	if(!$ex && $current)$bt=self::game($p);
	elseif($ex && $current)$bt=help('loto_played','alert');
	elseif($ex && !$current)$bt=self::results($r,$ex);
	else $bt=div(hlpxt('loto_finished').' '.$maxday,'paneb');
	if($current)$bt.=div(lang('time left').' : '.Build::leftime($end),'nfo');
	else $bt.=div(self::winner($id,$r['result']),'nfo');
	$ret.=div($bt,'','loto'.$id);
	return $ret;}

static function stream($p){return appx::stream($p);}

#call (read)
static function tit($p){return appx::tit($p);}

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