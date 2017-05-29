<?php

class pray{
static $private='1';
static $a='pray';
static $db='pray';
static $cb='prwrp';
static $cols=['txt','day','pub'];
static $typs=['text','date','int'];
static $conn=0;
static $open=1;

function __construct(){
	$r=['a','db','cb','cols','conn'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create('pray_group',['bid'=>'int','uid'=>'int'],1);
	Sql::create('pray_valid',['bid'=>'int','uid'=>'int','day'=>'date','ok'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function headers(){
	Head::add('csscode','
.minibtn{padding:4px;}
.minibtn tr:nth-of-type(1n){background:none;}
.minibtn tr:nth-of-type(2n){background:none;}
.minicon,.success{display:block; background:none; text-align:center;}
.minicon.active{background:lightgreen; border:1px solid green;}
.minicon.disactive{background:#efa2a2; border:1px solid red;}
.minicon:hover{background:#f4f4f4;}
.minicon.active:hover{background:palegreen;}
.minicon.disactive:hover{background:#ffb2b2;}
.minicon.off{background:#cfcfcf;}
.success.yes{background:lightgreen;}
.success.no{background:#ffb2b2;}
.text{background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
');}

#edit
static function del($p){
	$p['db2']='pray_group';
	return appx::del($p);}

static function modif($p){
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function edit($p){
	$p['conn']=self::$conn;
	$p['collect']='pray_valid';
	$p['help']='pray_edit';
	return appx::edit($p);}

static function collect($p){
	return appx::collect($p);}

static function save($p){
	return appx::save($p);}

static function create($p){
	return appx::create($p);}

#check
static function checkDay($p){
	if($p['status']){
		$id=Sql::read('id','pray_valid','v','where bid="'.$p['id'].'" and uid="'.ses('uid').'" and day="'.$p['day'].'"');
		if($p['status']==1)Sql::update('pray_valid','ok',2,$id);
		elseif($p['status']==2)Sql::update('pray_valid','ok',1,$id);}
	else Sql::insert('pray_valid',[$p['id'],ses('uid'),$p['day'],1]);
	return self::week($p);}

#week
static function week($p){
	$id=$p['id']; $now=time(); $usr=ses('user'); 
	$date=Sql::read('day',self::$db,'v',$id); $firstDay=strtotime($date);
	$w='left join login on uid=login.id where bid='.$id;
	$rusr=Sql::read('name','pray_group','rv',$w);
	$rvalid=Sql::read('name,day,ok','pray_valid','kkv',$w);
	for($i=0;$i<7;$i++)$rb[0][]=strftime('%a %d/%m',$firstDay+(86400*$i));//headers
	for($i=0;$i<7;$i++)$rdates[]=date('Y-m-d',$firstDay+(86400*$i));//headers
	//pr($rusr);//0=>dav
	//pr($rvalid);//dav=>[date=>1/2]
	//scores
	if($rvalid)foreach($rvalid as $k=>$v){$rord[$k]=0;
		if($v)foreach($v as $vb)if($vb==1)$rord[$k]+=1; else $rord[$k]=0;}
	//order
	if($rusr)foreach($rusr as $k=>$v)$rok[$v]=isset($rord[$v])?$rord[$v]:'';
	//view
	if(isset($rok)){//arsort($rok);//pr($rok);
	foreach($rok as $k=>$v){
		for($i=0;$i<7;$i++){
			$currentTime=$firstDay+(86400*$i);
			$currentDate=date('Y-m-d',$currentTime);
			if(isset($rvalid[$k][$currentDate]))
				$ok=$rvalid[$k][$currentDate];
			else $ok=0;
			if($currentTime<=$now){
				if($ok==2){$c=' disactive'; $ico=ico('close');}
				elseif($ok==1){$c=' active'; $ico=ico('check');}
				else{$c=''; $ico=ico('minus');}
				if($k==$usr)$bt=aj('rv'.$id.'|pray,checkDay|id='.$id.',status='.$ok.',day='.$currentDate,$ico,'minicon'.$c);
				else $bt=tag('span','class=minicon opac'.$c,$ico);}
			else $bt=span('-','minicon off');
			if($ok==1)$rc[$k][]=1;
			$rb[$k][]=$bt;}
		//score
		if(isset($rc[$k])){
			$n=count($rc[$k]);
			if($n>=7)$c=' yes'; else $c=' no';
			if($firstDay+592200<ses('time'))$rb[$k][]=span($n,'success'.$c);
			if($n>=7)$rd[$k][]=1;}//else $rd[$k][]=0;
	}}
	//render
	$ret=Build::table($rb,'minibtn',1,1);
	//total
	if(isset($rd)){
		$n=count($rd);
		if($n>=7)$msg='success'; else $msg='fail';
		$ret.=div(lang($msg).' ('.$n.'*7)',$n>=7?'valid':'alert');}
	return $ret;}

#play
static function participation($p){
	if($p['subscribe']=='ok'){$p['op']=1;
		Sql::insert('pray_group',[$p['id'],ses('uid')]);}
	elseif($p['subscribe']=='ko'){
		Sql::delete('pray_group',['bid'=>$p['id'],'uid'=>$p['uid']]);
		Sql::delete('pray_valid',['bid'=>$p['id'],'uid'=>$p['uid']]);}
	return self::build($p);}

#pane
static function build($p){$id=$p['id']; $op=val($p,'op'); $ret='';
	$n=Sql::read('count(id)','pray_group','v','where bid='.$id);
	$bt=ico('bars').' '.$n.' '.langs('participant',$n);
	$ret.=toggle('rv'.$id.'|pray,week|id='.$id,$bt,'nfo',$op?1:0);
	if($uid=ses('uid')){
		$uidok=Sql::read('id','pray_group','v','where bid="'.$id.'" and uid="'.$uid.'"');
		$ex=Sql::read('id','pray_valid','v','where bid="'.$id.'" and uid="'.$uid.'"');
		$j='ev'.$id.'|pray,participation|id='.$id.',uid='.$uid;
		if(!$uidok)$ret.=aj($j.',subscribe=ok',lang('participate'),'btsav').' ';
		elseif(!$ex)$ret.=aj($j.',subscribe=ko',lang('unsubscribe'),'btdel').' ';
		$ret.=hlpbt('pray_how');}
	else $uid='';
	if($op)$week=self::week(['id'=>$id]); else $week='';
	$ret.=div($week,'','rv'.$id);
	return div($ret,'','ev'.$id);}

#stream
static function play($p){$id=$p['id']; $p['conn']=self::$conn;
	$w='left join profile on '.self::$db.'.uid=profile.puid where '.self::$db.'.id='.$id;
	$r=Sql::read('uid,txt,day,pname',self::$db,'ra',$w);
	if(!$r)return lang('entry not exists');
	$bt=href('/app/pray/'.$id,ico('link'));
	if(ses('uid'))
		if($rid=val($p,'rid'))$bt.=insertbt(lang('use'),$id.':pray',$rid);
	$ret=div($bt,'right');
	if(val($p,'conn')=='no')$txt=$r['txt']; else $txt=nl2br($r['txt']);//!!
	$go=aj('mee'.$id.'|pray,play|op=1,id='.$id,'#'.$id,'btn');
	$time=strtotime($r['day']);
	if($time+592200<ses('time'))$c='alert '; else $c='valid ';
	$date=span(lang('date',1).' : '.$r['day'],$c.'nfo');
	$ret.=div($go.' '.span(lang('by'),'small').' '.$r['pname'].' '.$date);
	$ret.=div($txt,'tit');
	$ret.=self::build($p);
	return div($ret,'paneb');}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

static function call($p){
	return self::play($p);}

//com (edit)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	self::install();
	return appx::content($p);}
}

?>