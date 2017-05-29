<?php

class petition{
static $private='1';
static $a='petition';
static $db='petition';
static $cb='ptwrp';
static $cols=['tit','txt','cl'];
static $typs=['var','var','int'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}
	
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create('petition_vals',['bid'=>'int','uid'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function injectJs(){
	return '';}
	
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#editor
static function del($p){$p['db2']='petition_vals'; return appx::del($p);}
static function modif($p){return appx::modif($p);}
static function save($p){return appx::save($p);}
static function create($p){return appx::create($p);}
static function form($p){return appx::form($p);}

static function edit($p){
	$p['collect']='petition_vals';
	return appx::edit($p);}

/*static function collect($p){
	return appx::collect($p);}*/

static function sign($p){$id=val($p,'id');
	$nid=Sql::insert('petition_vals',[$id,ses('uid')]);
	return self::play($p);}

static function unsign($p){$id=val($p,'id'); $vrf='';
	Sql::delete('petition_vals',['bid'=>$id,'uid'=>ses('uid')],'',1);
	return self::play($p);}

static function answers($p){$id=val($p,'id'); $ret='';
	$r=Sql::read_inner('name,dateup','petition_vals','login','uid','rr','where bid='.$id);
	if($r)$ret=div(count($r).' '.lang('signatures'),'valid');
	if($r)array_unshift($r,[lang('user'),lang('date')]);
	return $ret.Build::table($r);}

static function already($id){
	return Sql::read('id','petition_vals','v','where uid='.ses('uid').' and bid='.$id);}
	
static function play($p){$id=val($p,'id'); $rid=val($p,'rid'); $nb=''; $cancel='';
	if($id){$r=Sql::read('id,tit,txt,cl,dateup',self::$db,'ra',$id);
		$n=Sql::read('count(id)','petition_vals','v','where bid='.$id);}
	$ret=div($r['tit'],'tit').div($r['txt'],'txt');
	if($n)$nb=' '.span($n.' '.langs('signature',$n),'btok');
	if($r['cl'])$bt=help('petition closed','alert');
	elseif(self::already($id)){
		$cancel=aj('ptcbk'.$id.'|petition,unsign|id='.$id,langp('remove'),'btdel');
		$bt=div(ico('check').' '.hlpxt('petition_filled'),'valid');}
	else $bt=aj('ptcbk'.$id.'|petition,sign|id='.$id.',rid='.$rid,langp('sign'),'btsav');
	$ret.=div($bt.$nb.$cancel);
	return div($ret,'paneb');}

static function template(){
	return '[[[_date*class=date:span] _tit _bt _insert _answ*class=tit:div][_txt*class=txt:div]*class=menu:div]';}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (read)
static function call($p){
	return div(self::play($p),'','ptcbk'.$p['id']);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>
