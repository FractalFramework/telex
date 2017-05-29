<?php

class meet{
static $private='1';
static $a='meet';
static $db='meet';
static $cb='mtcbk';
static $cols=['txt','loc','day','pub'];
static $typs=['var','var','date','int'];

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create('meet_valid',array('bid'=>'int','uid'=>'int','ok'=>'int'),1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function headers(){
	Head::add('csscode','
.line{display:block; background:#eee; padding:4px; margin:8px 0;}
.line.active{background:lightgreen; border:1px solid green;}
.line.disactive{background:#efa2a2; border:1px solid red;}
.line:hover{background:white;}
.line.active:hover{background:palegreen;}
.line.disactive:hover{background:#ffb2b2;}
.text{background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
');}

#edit
static function gps($d){
	$ret=input('loc',$d,28);
	$ret=input('loc',$d,28);
	$ret.=aj('cbkmap|map,request||loc',lang('ok',1),'btn');
	return $ret.span('','','cbmap');}

static function modif($p){
	$r=vals($p,['txt','day','loc']);
	if($p['id'])Sql::updates(self::$db,$r,$p['id']);
	return self::play($p);}

#editor
static function form($p){return appx::form($p);}

static function edit($p){
	$p['collect']='meet_valid';
	return appx::edit($p);}

static function collect($p){return appx::collect($p);}
static function del($p){return appx::del($p);}

//static function save($p){return appx::save($p);}
static function save($p){
	$r=[ses('uid')]; foreach(self::$cols as $v)$r[]=val($p,$v,0);
	if($p['txt'])$nid=Sql::insert(self::$db,$r);
	if(isset($nid))return div(self::play(['id'=>$nid]),'','mee'.$nid);}

//static function create($p){return appx::create($p);}
static function create(){
	$ret=input('day',date('Y-m-d',time()),8);
	$ret.=input('loc','','32',lang('address')).br();
	$ret.=textarea('txt','',70,4,lang('presentation'),'',216).br();
	$ret.=aj(self::$cb.'|meet,save||txt,day,loc',lang('save'),'btsav');
	return $ret;}

#check
static function checkDay($p){//p($p);
	if($p['status']==1)Sql::update('meet_valid','ok',2,$p['uid']);
	elseif($p['status']==2)Sql::update('meet_valid','ok',1,$p['uid']);
	return self::rendezvous($p);}

#rendezvous
static function rendezvous($p){
	$id=$p['id']; $uid=ses('uid');
	$r=Sql::read('id,uid,ok','meet_valid','rr','where bid='.$id);
	if($r)foreach($r as $k=>$v){
		$name=profile::name($v['uid']);
		if($v['ok']==2){$c=' disactive'; $ico=ico('close').$name.' '.span(lang('canceled'),'btn');}
		else{$c=' active'; $ico=ico('check').$name;}
		if($v['uid']!=$uid)$bt=tag('span','class=line opac'.$c,$ico);
		else $bt=aj('rv'.$id.'|meet,checkDay|id='.$id.',uid='.$v['id'].',status='.$v['ok'],$ico,'line'.$c);
		$rb[]=$bt;}
	if(isset($rb))return Build::scroll($rb,10,'400');}

#play
static function participation($p){
	if($p['subscribe']=='ok')
		Sql::insert('meet_valid',[$p['id'],ses('uid'),1]);
	elseif($p['subscribe']=='ko')
		Sql::delete('meet_valid',$p['uid']);
	return self::build($p);}

#pane
static function build($p){$id=$p['id'];
	$n=Sql::read('count(id)','meet_valid','v','where bid='.$id);
	$bt=$n. ' '.lang('participants');
	$ret=toggle('rv'.$id.'|meet,rendezvous|id='.$id,$bt,'nfo').' ';
	if($uid=ses('uid')){
		$uid=Sql::read('id','meet_valid','v','where bid="'.$id.'" and uid="'.$uid.'"');
		$j='ev'.$id.'|meet,participation|id='.$id.',uid='.$uid;
		if(!$uid)$ret.=aj($j.',subscribe=ok',lang('participate'),'btsav').' ';
		else $ret.=aj($j.',subscribe=ko',lang('unsubscribe'),'btdel').' ';}
	$ret.=div('','','rv'.$id);
	return div($ret,'','ev'.$id);}

#stream
static function play($p){$id=$p['id']; $rid=val($p,'rid');
	$r=Sql::read('uid,txt,day,loc',self::$db,'ra',$id);
	if(!$r)return lang('entry not exists');
	$bt=href('/app/meet/'.$id,ico('link'));
	if($rid)$bt.=insertbt(lang('use'),$id.':meet',$rid);
	$ret=div($bt,'right');
	if(val($p,'conn')=='no')$txt=$r['txt']; else $txt=nl2br($r['txt']);
	$go=aj('mee'.$id.'|meet,play|id='.$id,'#'.$id,'btn');
	//$go=href('/app/meet/'.$id,pic('url').' #'.$id,'btn');
	$name=profile::name($r['uid'],1);
	if(strtotime($r['day'])<ses('time'))$c='alert '; else $c='valid ';
	$date=span($r['day']?lang('date',1).' : '.$r['day']:lang('undefined'),$c.'date');
	$gps=aj('popup|map,request|request='.$r['loc'],pic('gps').' '.$r['loc'],'btn');
	$ret.=div($go.' '.span(lang('by'),'small').' '.$name.' '.$date.' '.$gps);
	$ret.=div($txt,'tit');
	$ret.=self::build(['id'=>$id]);
	return div($ret,'paneb');}

static function stream($p){
	return appx::stream($p);}

static function call($p){$id=val($p,'id');
	return div(self::play($p),'','mee'.$id);}
	
static function tit($p){
	return appx::tit($p);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}

?>