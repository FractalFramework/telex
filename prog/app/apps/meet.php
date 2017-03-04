<?php

class meet{
static $private='1';

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
static function voc(){
	return array();}

//install
static function install(){
	Sql::create('meet',array('user'=>'var','txt'=>'var','day'=>'date','loc'=>'var'),1);
	Sql::create('meet_valid',array('idMeet'=>'int','user'=>'var','ok'=>'int'),1);}

#check
static function checkDay($p){//p($p);
	if($p['status']==1)Sql::update('meet_valid','ok',2,$p['uid']);
	elseif($p['status']==2)Sql::update('meet_valid','ok',1,$p['uid']);
	return self::rendezvous($p);}

#rendezvous
static function rendezvous($p){
	$id=$p['id']; $user=ses('user');
	$r=Sql::read('id,user,ok','meet_valid','rr','where idMeet='.$id);
	if($r)foreach($r as $k=>$v){
		$name=profile::name($v['user']);
		if($v['ok']==2){$c=' disactive'; $ico=ico('close').$name.' '.span(lang('canceled'),'alert');}
		else{$c=' active'; $ico=ico('check').$name;}
		if($v['user']!=$user)$bt=tag('span','class=line opac'.$c,$ico);
		else $bt=aj('rv'.$id.'|meet,checkDay|id='.$id.',uid='.$v['id'].',status='.$v['ok'],$ico,'line'.$c);
		$rb[]=$bt;}
	if(isset($rb))return Build::scroll($rb,5,'200');}

#event
static function participation($p){
	if($p['subscribe']=='ok')
		Sql::insert('meet_valid',[$p['id'],ses('user'),1]);
	elseif($p['subscribe']=='ko')
		Sql::delete('meet_valid',$p['uid']);
	return self::build($p);}

#edit
static function gps($d){
	$ret=input('loc',$d,28);
	$ret=input('loc',$d,28);
	$ret.=aj('cbkmap|map,request||loc',lang('ok',1),'btn');
	return $ret.span('','','cbmap');}

static function modif($p){
	$r=vals($p,['txt','day','loc']);
	if($p['id'])Sql::updates('meet',$r,$p['id']);
	return self::event($p);}

static function edit($p){$id=$p['id'];
	$r=Sql::read('txt,day,loc','meet','ra','where id='.$id);
	$ret=input('day',$r['day'],8);
	$ret.=input('loc',$r['loc'],'32',lang('address')).br();
	$ret.=textarea('txt',$r['txt'],70,4,lang('text'),'','',140).br();
	$ret.=span('','small right','strcnttext');
	$ret.=aj('mee'.$id.'|meet,modif|id='.$id.'|txt,day,loc',pic('save'),'btsav');
	return $ret;}

static function del($p){$id=$p['id'];
	if(!isset($p['ok']))
		return aj('meetcontent|meet,del|ok=1,id='.$id,langp('really?'),'btdel');
	elseif($id){Sql::delete('meet',$p['id']);
		Sql::delete('meet_valid',$id,'idMeet');}
	return self::stream($p);}

static function save($p){
	if($p['txt'])$nid=Sql::insert('meet',array(ses('user'),$p['txt'],$p['date'],$p['loc']));
	if(isset($nid))return div(self::event(['id'=>$nid]),'','mee'.$nid);}

static function create(){
	$ret=input('date',date('Y-m-d',time()),8);
	$ret.=input('loc','','32',lang('address')).br();
	$ret.=textarea('txt','',70,4,lang('presentation'),'','',140).br();
	$ret.=aj('meetcontent,,x|meet,save||txt,date,loc',lang('save'),'btsav');
	return $ret;}

#pane
static function build($p){$id=$p['id'];
	$n=Sql::read('count(id)','meet_valid','v','where idMeet='.$id);
	$bt=$n. ' '.lang('participants');
	$ret=toggle('rv'.$id.'|meet,rendezvous|id='.$id,$bt,'nfo').' ';
	if($user=ses('user')){
		$uid=Sql::read('id','meet_valid','v','where idMeet="'.$id.'" and user="'.$user.'"');
		$j='ev'.$id.'|meet,participation|id='.$id.',uid='.$uid;
		if(!$uid)$ret.=aj($j.',subscribe=ok',lang('participate'),'btsav').' ';
		else $ret.=aj($j.',subscribe=ko',lang('unsubscribe'),'btdel').' ';}
	$ret.=div('','','rv'.$id);
	return div($ret,'','ev'.$id);}

#stream
static function event($p){$id=$p['id'];
	$r=Sql::read('user,txt,day,loc','meet','ra','where id='.$id);
	if(!$r)return lang('entry not exists');
	$bt=href('/app/meet/'.$id,ico('link'));
	if(ses('user'))
		if($xid=val($p,'xid'))$bt.=insertbt(lang('use'),$id.':meet',$xid);
	if($r['user']==ses('user')){
		$bt.=aj('panxt'.$id.'|meet,edit|id='.$id,pic('modif'),'btn');
		$bt.=aj('meetcontent|meet,del|id='.$id,pic('delete'),'btdel');}
	$ret=div($bt,'right');
	if(val($p,'brut'))$txt=$r['txt']; else $txt=nl2br($r['txt']);
	//$go=aj('mee'.$id.'|meet,event|id='.$id,pic('go').' #'.$id,'btn');
	$go=href('/app/meet/'.$id,pic('go').' #'.$id,'btn');
	$name=profile::name($r['user'],1);
	if(strtotime($r['day'])<ses('time'))$c='alert '; else $c='valid ';
	$date=span(lang('date',1).' : '.$r['day'],$c.'nfo');
	$gps=aj('popup|map,request|request='.$r['loc'],pic('gps').' '.$r['loc'],'btn');
	$ret.=div($go.' '.span(lang('by'),'small').' '.$name.' '.$date.' '.$gps);
	$ret.=div($txt,'txt','panxt'.$id);
	$ret.=self::build(['id'=>$id]);
	return div($ret,'pane');}

static function stream($p){$ret='';
	$r=Sql::read('id','meet','rv','order by day desc');
	if($r)foreach($r as $v){$p['id']=$v;
		$ret.=div(self::event($p),'','mee'.$v);}
	return $ret;}

//conn
static function call($p){
	$p['id']=val($p,'id');
	if($p['id'])return div(self::event($p),'','mee'.$p['id']);}
//tlex
static function com($p){
	$p['xid']=val($p,'rid');
	return self::content($p);}

#content
static function content($p){$ret='';
	//self::install();
	$p['id']=val($p,'id',val($p,'param'));
	$ret=aj('meetcontent|meet,stream',ico('home'),'btn');
	//$ret=href('/app/meet',ico('home'));
	$ret.=hlpbt('meet_help');
	if(ses('uid'))$ret.=aj('pagup|meet,create',ico('plus'),'btn');
	$ret.=br().br();
	if($p['id'])$res=self::call($p);
	else $res=self::stream($p);
	$ret.=div($res,'','meetcontent');
	return $ret;}
}

?>