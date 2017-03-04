<?php

class pray{
static $private='1';

static function headers(){
	Head::add('csscode','
.minibtn{padding:4px; font-size:smaller; text-align:center;}
.minicon,.success{display:block; width:54px; background:#fefefe; text-align:center;}
.minicon.active{background:lightgreen; border:1px solid green;}
.minicon.disactive{background:#efa2a2; border:1px solid red;}
.minicon:hover{background:white;}
.minicon.active:hover{background:palegreen;}
.minicon.disactive:hover{background:#ffb2b2;}
.success.yes{background:lightgreen;}
.success.no{background:#ffb2b2;}
.text{background:white; border-radius:2px; padding:10px; margin:10px 0; font-size:medium;}
');}

//install
static function install(){
	Sql::create('pray_lead',['user'=>'var','txt'=>'var','day'=>'date'],1);
	Sql::create('pray_group',['idPray'=>'int','user'=>'var'],1);
	//Sql::drop('pray_event');
	Sql::create('pray_valid',['idPray'=>'int','user'=>'var','day'=>'date','ok'=>'int'],1);}

#check
static function checkDay($p){
	if($p['status']){
		$id=Sql::read('id','pray_valid','v','where idPray="'.$p['id'].'" and user="'.$p['user'].'" and day="'.$p['day'].'"');
		if($p['status']==1)Sql::update('pray_valid','ok',2,$id);
		elseif($p['status']==2)Sql::update('pray_valid','ok',1,$id);}
	else Sql::insert('pray_valid',[$p['id'],ses('user'),$p['day'],1]);
	return self::week($p);}

#week
static function week($p){
	$user=ses('user'); $id=$p['id']; $now=time();
	$date=Sql::read('day','pray_lead','v','where id='.$id); $firstDay=strtotime($date);
	$rusr=Sql::read('user','pray_group','rv','where idPray='.$id);
	//$w='left join profile on pray_group.user=profile.pusr ';
	//$rusr=Sql::read('pname','pray_group','rv',$w.'where idPray='.$id);
	$rvalid=Sql::read('user,day,ok','pray_valid','kkv','where idPray='.$id);
	for($i=0;$i<7;$i++)$rb[0][]=strftime('%a %d/%m',$firstDay+(86400*$i));//headers
	if($rusr)foreach($rusr as $k=>$v){
		for($i=0;$i<7;$i++){
			$currentTime=$firstDay+(86400*$i);
			$currentDate=date('Y-m-d',$currentTime);
			if(isset($rvalid[$v][$currentDate]))
				$ok=$rvalid[$v][$currentDate];
			else $ok=0;
			if($currentTime<=$now){
				if($ok==2){$c=' disactive'; $ico=ico('close');}
				elseif($ok==1){$c=' active'; $ico=ico('check');}
				else{$c=''; $ico=ico('minus');}
				if($v!=$user)$bt=tag('span','class=minicon opac'.$c,$ico);
				else $bt=aj('rv'.$id.'|pray,checkDay|id='.$id.',user='.$v.',status='.$ok.',day='.$currentDate,$ico,'minicon'.$c);}
			else $bt=span(ico('ellipsis-h'),'minicon');
			if($ok==1)$rc[$v][]=1;
			$rb[$v][]=$bt;}
		//score
		if(isset($rc[$v])){
			$n=count($rc[$v]);
			if($n>=7)$c=' yes'; else $c=' no';
			if($firstDay+592200<ses('time'))$rb[$v][]=span($n,'success'.$c);
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
static function participation($p){
	if($p['subscribe']=='ok'){$p['op']=1;
		Sql::insert('pray_group',[$p['id'],ses('user')]);}
	elseif($p['subscribe']=='ko')
		Sql::delete('pray_group',$p['uid']);
	return self::build($p);}

#edit
static function modif($p){
	$r=vals($p,['txt','day']);
	if($p['id'])Sql::updates('pray_lead',$r,$p['id']);
	return self::event($p);}

static function edit($p){$id=$p['id'];
	$r=Sql::read('txt,day','pray_lead','ra','where id='.$id);
	$ret=input('day',$r['day'],8).br();
	$ret.=textarea('txt',$r['txt'],60,4,lang('description'),'','',140).br();
	$ret.=aj('mee'.$id.'|pray,modif|id='.$id.'|txt,day',lang('save'),'btsav');
	return $ret;}

static function del($p){$id=$p['id'];
	if(!isset($p['ok']))
		return aj('praycontent|pray,del|ok=1,id='.$id,lang('really?'),'btdel');
	elseif($id){Sql::delete('pray_lead',$p['id']);
		Sql::delete('pray_group',$id,'idPray');}
	return self::stream($p);}

static function save($p){
	if($p['txt'])$p['id']=Sql::insert('pray_lead',array(ses('user'),$p['txt'],$p['date']));
	if(isset($p['id']))return self::event($p);}

static function create($p){
	$ret=input('date',date('Y-m-d',time()),8).br();
	$ret.=textarea('txt','',60,4,lang('description'),'','',140).br();
	$ret.=aj('praycontent,,x|pray,save|xid='.val($p,'xid').'|txt,date',lang('save'),'btsav');
	return $ret;}

#pane
static function build($p){$id=$p['id']; $op=val($p,'op'); $ret='';
	$n=Sql::read('count(id)','pray_group','v','where idPray='.$id);
	$bt=ico('bars').' '.$n.' '.plurial('participant',$n);
	if($user=ses('user')){
		$uid=Sql::read('id','pray_group','v','where idPray="'.$id.'" and user="'.$user.'"');
		$ex=Sql::read('id','pray_valid','v','where idPray="'.$id.'" and user="'.$user.'"');
		$j='ev'.$id.'|pray,participation|id='.$id.',uid='.$uid;
		if(!$uid)$ret.=aj($j.',subscribe=ok',lang('participate'),'btsav').' ';
		elseif(!$ex)$ret.=aj($j.',subscribe=ko',lang('unsubscribe'),'btdel').' ';}
	else $uid='';
	$ret.=toggle('rv'.$id.'|pray,week|id='.$id,$bt,'nfo',$op?1:0);
	if($op)$week=self::week(['id'=>$id]); else $week='';
	$ret.=div($week,'','rv'.$id);
	return div($ret,'','ev'.$id);}

#stream
static function event($p){$id=$p['id'];
	$w='left join profile on pray_lead.user=profile.pusr where pray_lead.id='.$id;
	$r=Sql::read('user,txt,day,pname','pray_lead','ra',$w);
	if(!$r)return lang('entry not exists');
	$bt=href('/app/pray/'.$id,ico('link'));
	if(ses('user'))
		if($xid=val($p,'xid'))$bt.=insertbt(lang('use'),$id.':pray',$xid);
	if($r['user']==ses('user')){
		$bt.=aj('panxt'.$id.'|pray,edit|id='.$id,lang('modif'),'btn');
		$bt.=aj('praycontent|pray,del|id='.$id,lang('delete'),'btdel');}
	$ret=div($bt,'right');
	if(val($p,'brut'))$txt=$r['txt']; else $txt=nl2br($r['txt']);
	$go=aj('mee'.$id.'|pray,event|op=1,id='.$id,pic('go').' #'.$id,'btn');
	$time=strtotime($r['day']);
	if($time+592200<ses('time'))$c='alert '; else $c='valid ';
	$date=span(lang('date',1).' : '.$r['day'],$c.'nfo');
	$ret.=div($go.' '.span(lang('by'),'small').' '.$r['pname'].' '.$date);
	$ret.=div($txt,'txt','panxt'.$id);
	$ret.=self::build($p);
	return div($ret,'pane');}

static function stream($p){$ret='';
	if(val($p,'known'))$r=Sql::read_inner('pray_lead.id','pray_group','pray_lead','idPray','rv','where pray_group.user="'.ses('user').'" order by day desc');
	else $r=Sql::read('id','pray_lead','rv','order by day desc');
	if($r)foreach($r as $v){$p['id']=$v;
		$ret.=div(self::event($p),'','mee'.$v);}
	return $ret;}

//conn
static function call($p){$p['id']=val($p,'id');
	if($p['id'])return div(self::event($p),'','mee'.$p['id']);}
//tlex
static function com($p){$p['xid']=val($p,'rid'); $p['known']=1;
	return self::content($p);}

#content
static function content($p){$ret='';
	//self::install();
	$p['id']=val($p,'id',val($p,'param')); $xid=val($p,'xid');
	$ret=aj('praycontent|pray,stream|xid='.$xid,ico('home'),'btn');
	//$ret=href('/app/pray',ico('home'));
	if(ses('uid'))$ret.=aj('pagup|pray,create|xid='.$xid,ico('plus'),'btn');
	$ret.=hlpbt('pray_app').' ';
	$ret.=hlpbt('pray_description',ico('question')).' ';
	$ret.=hlpbt('pray_how',ico('book'));
	$ret.=br().br();
	if($p['id'])$res=self::call($p+['op'=>1]);
	else $res=self::stream($p);
	$ret.=div($res,'','praycontent');
	return $ret;}
}

?>