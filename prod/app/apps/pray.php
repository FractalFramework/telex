<?php

class pray{
static $private='1';

static function headers(){
	Head::add('csscode','
.minibtn{padding:4px; font-size:smaller; text-align:center;}
.minicon,.success{display:block; width:54px; background:#eee; text-align:center;}
.minicon.active{background:lightgreen; border:1px solid green;}
.minicon.disactive{background:#efa2a2; border:1px solid red;}
.minicon:hover{background:white;}
.minicon.active:hover{background:palegreen;}
.minicon.disactive:hover{background:#ffb2b2;}
.success.yes{background:lightgreen;}
.success.no{background:#ffb2b2;}
.text{background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
');}
static function voc(){
	return array();}

//install
static function install(){
	Sql::create('pray_list',array('user'=>'var','text'=>'var'));
	Sql::create('pray_event',array('idPray'=>'int','user'=>'var','day'=>'date'));
	Sql::create('pray_group',array('user'=>'var','idEvent'=>'int'));
	Sql::create('pray_valid',array('idEvent'=>'int','user'=>'var','day'=>'date','ok'=>'int'),1);}

/*---*/

#check
static function checkDay($p){
	if($p['status']){
		$id=Sql::read('id','pray_valid','v','where idEvent="'.$p['idEv'].'" and user="'.$p['user'].'" and day="'.$p['day'].'"');
		if($p['status']==1)Sql::update('pray_valid','ok',2,$id);
		elseif($p['status']==2)Sql::update('pray_valid','ok',1,$id);}
	else Sql::insert('pray_valid',array($p['idEv'],ses('user'),$p['day'],1));
	return self::eventPane($p);}

#week
private static function week($p){//idPray,idEv
	$user=ses('user'); $now=time(); $id=$p['idPray']; $idEv=$p['idEv'];
	$startDate=Sql::read('day','pray_event','v','where id='.$idEv);
	$rusr=Sql::read('id,user','pray_group','kv','where idEvent='.$idEv);
	$rvalid=Sql::read('user,day,ok','pray_valid','kkv','where idEvent="'.$idEv.'"');
	$firstDay=strtotime($startDate);
	for($i=0;$i<7;$i++)$rb[0][]=strftime('%a %d/%m',$firstDay+(86400*$i));//headers
	if($rusr)foreach($rusr as $k=>$v){$nb=8;
		for($i=1;$i<8;$i++){
			$currentTime=$firstDay+(86400*$i);
			$currentDate=date('Y-m-d',$currentTime);
			if(isset($rvalid[$v][$currentDate]))
				$ok=$rvalid[$v][$currentDate];
			else $ok=0;
			if($ok==1)$rc[$v][]=1;
			if($currentTime<=$now){
				if($ok==2){$c=' disactive'; $ico=pic('close');}
				elseif($ok==1){$c=' active'; $ico=pic('check');}
				else{$c=''; $ico=pic('minus');}
				if($v!=$user)$bt=tag('span','class=minicon opac'.$c,$ico);
				else $bt=aj('ev'.$id.'|pray,checkDay|idPray='.$id.',idEv='.$p['idEv'].',user='.$v.',status='.$ok.',day='.$currentDate,$ico,'minicon'.$c);}
			else $bt=' ';
			$rb[$v][]=$bt;
		}
		//score
		if(isset($rc[$v])){
			$n=count($rc[$v]);
			if($n>=7)$c=' yes'; else $c=' no';
			$rb[$v][]=span($n,'success'.$c);
			if($n>=7)$rd[$v][]=1;} //else $rd[$v][]=0;
	}
	//render
	$ret=Build::table($rb,'minibtn','',1);
	//total
	if(isset($rd)){
		$n=count($rd);
		if($n>=7)$msg='success'; else $msg='fail';
		$ret.=div(lang($msg).' ('.$n.')',$n>=7?'valid':'alert');}
	return $ret;}

#event
static function eventParticipation($p){
	if($p['subscribe']=='ok')
		Sql::insert('pray_group',array(ses('user'),$p['idEv']));
	elseif($p['subscribe']=='ko')
		Sql::delete('pray_group',$p['uid']);
	return self::eventPane($p);}
static function eventDel($p){
	$id=val($p,'idPray'); $idEv=val($p,'idEv');
	if(!isset($p['ok']))
		return aj('ev'.$id.'|pray,eventDel|ok=1,idEv='.$idEv.',idPray='.$id,lang('really?'),'btdel');
	elseif($idEv){Sql::delete('pray_event',$idEv);
		Sql::delete('pray_valid',$idEv,'idEvent');
		Sql::delete('pray_group',$idEv,'idEvent');}
	return self::announce($p);}
static function eventSave($p){
	$date=date('Y-m-d',strtotime($p['evDate']));
	if($p['evDate'])Sql::insert('pray_event',array($p['idPray'],ses('user'),$date));
	return self::announce($p);}
static function eventAdd($p){
	$ret=input('evDate',date('Y-m-d',time()),8);
	$ret.=aj('praycontent|pray,eventSave|idPray='.$p['idPray'].'|evDate',lang('create'),'btsav');
	return $ret;}

#pane
static function eventPane($p){//idPray,idEv
	$id=val($p,'idPray'); $idEv=val($p,'idEv'); $bt='';
	if(!$id && $idEv)
		$p['idPray']=Sql::read('idPray','pray_event','v','where id='.$idEv);
	$r=Sql::read('day,user','pray_event','ry','where id='.$idEv);
	list($eventDate,$userEvent)=$r;
	$back=aj('pry'.$id.'|pray,announce|idPray='.$id,pico('back'),'btn');
	$date=aj('ev'.$id.'|pray,eventPane|idPray='.$id.',idEv='.$idEv,pic('refresh').$eventDate,'btn');
	$ret=$back.' '.$date.' '.lang('created by').' '.profile::name($userEvent,1);
	//$bt=href('/app/pray/idPray='.$id.'/idEv='.$idEv,pic('link'));
	//register
	$user=ses('user');
	if(ses('user')){
		$uid=Sql::read('id','pray_group','v','where idEvent="'.$idEv.'" and user="'.$user.'"');
		if(!$uid)$bt.=aj('ev'.$id.'|pray,eventParticipation|idPray='.$id.',idEv='.$idEv.',subscribe=ok,uid='.$uid,lang('participate'),'btsav');
		//else $bt.=aj('ev'.$id.'|pray,eventParticipation|idPray='.$id.',idEv='.$idEv.',subscribe=ko,uid='.$uid,lang('unsubscribe'),'btdel');
	}
	//admin
	if($userEvent==ses('user'))
		$bt.=aj('ev'.$id.'|pray,eventDel|idEv='.$idEv.',idPray='.$id,lang('delete'),'btdel');
	$ret.=span($bt,'right');
	//calendar
	$ret.=self::week($p);
	return div($ret,'');}

#edit
static function announceUpdate($p){
	if($p['idPray'])Sql::update('pray_list','text',$p['text'],$p['idPray']);
	return self::announce($p);}

static function announceEdit($p){$id=$p['idPray'];
	$txt=Sql::read('text','pray_list','v','where id='.$id);
	$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
	$ret=tag('textarea',$r,$txt).br();
	$ret.=span('','small right','strcnttext');
	$ret.=aj('pry'.$id.'|pray,announceUpdate|idPray='.$id.'|text',lang('save'),'btsav');
	return $ret;}

static function announceDel($p){
	if(!isset($p['ok']))
		return aj('praycontent|pray,announceDel|ok=1,idPray='.$p['idPray'],lang('really?'),'btdel');
	elseif($p['idPray']){Sql::delete('pray_list',$p['idPray']);
		Sql::delete('pray_event',$p['idPray'],'idPray');}
	return self::announces($p);}

static function announceSave($p){
	if($p['text'])$nid=Sql::insert('pray_list',array(ses('user'),$p['text']));
	if(isset($nid))return self::announce(array('idPray'=>$nid));}

static function announceAdd(){
	$r=array('id'=>'text','cols'=>60,'rows'=>4,'onkeyup'=>'strcount(\'text\',255)');
	$ret=tag('textarea',$r,'').br();
	$ret.=span('','small right','strcnttext');
	$ret.=aj('praycontent,,x|pray,announceSave||text',lang('save'),'btsav');
	return $ret;}

private static function eventsList($id){$ret='';
	$r=Sql::read('id,day','pray_event','','where idPray='.$id.' order by day');
	if($r)foreach($r as $v){
		$tim=strtotime($v[1]);
		if($tim+604800>ses('time'))$c='btok'; else $c='btn';//1 week before
		$ret.=aj('ev'.$id.'|pray,eventPane|idPray='.$id.',idEv='.$v[0],$v[1],$c);}
	if($ret)return 'Sessions: '.$ret;}

#announces
static function announce($p){$id=$p['idPray'];
	$r=Sql::read('user,text','pray_list','ra','where id='.$id);
	if(!$r)return 'Annonce inexistante';
	//bt
	$bt=href('/app/pray/'.$id,pic('link'));
	if(ses('user')){
		if($xid=val($p,'xid'))$bt.=insertbt(lang('use'),$id.':pray',$xid);
		$bt.=dropdown('pray,eventAdd|idPray='.$id,lang('add event'),'btn');}
	if($r['user']==ses('user')){
		$bt.=aj('panxt'.$id.'|pray,announceEdit|idPray='.$id,lang('modif'),'btn');
		$bt.=aj('praycontent|pray,announceDel|idPray='.$id,lang('delete'),'btdel');}
	$bt=div($bt,'right');
	if(val($p,'brut'))$txt=$r['text']; else $txt=nl2br($r['text']);
	$txt=div($txt,'text','panxt'.$id);
	//events
	$events=self::eventsList($id);
	$go=aj('pry'.$id.'|pray,announce|idPray='.$id,pico('go').' #'.$id,'btn');
	$ret=$go.' '.lang('by').' '.profile::name($r['user'],1).$bt.br().$txt;
	$ret.=div($events,'','ev'.$id);
	return div($ret,'pane');}

static function announces($p){$ret='';
	$r=Sql::read('id','pray_list','rv','order by id desc');
	if($r)foreach($r as $v){$p['idPray']=$v;
		$ret.=div(self::announce($p),'','pry'.$v);}
	return $ret;}

/*---*/

//conn
static function call($p){
	$p['idPray']=val($p,'id');
	if($p['idPray'])return div(self::announce($p),'','pry'.$p['idPray']);}
//tlex
static function com($p){
	$p['xid']=val($p,'rid');
	return self::content($p);}

#content
static function content($p){$ret='';
	//self::install();
	//$logbt=ses('user')?ses('user'):'login';
	//$ret.=aj('popup|login|auth=2',pic('user').' '.$logbt,'btn right');
	$p['idPray']=val($p,'idPray',val($p,'param'));
	$ret=aj('praycontent|pray,announces',pic('home'),'btn');
	//$ret=href('/app/pray',pic('home'));
	if(ses('uid'))$ret.=aj('praycontent|pray,announceAdd',pic('plus'),'btn');
	$ret.=hlpbt('pray_help');
	$ret.=br().br();
	//root
	if(isset($p['idEv']))$res=self::eventPane($p);
	elseif($p['idPray'])$res=self::call($p);
	else $res=self::announces($p);
	$ret.=tag('div',array('id'=>'praycontent'),$res);
	return $ret;}
}

?>