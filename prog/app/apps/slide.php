<?php

class slide{
static $private='0';
static $a='slide';
static $db='slide';
static $cb='sld';
static $cols=['tit'];
static $typs=['var'];
static $open=1;

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

//install
static function install(){
	appx::install(['tit'=>'var']);
	Sql::create('slide_page',['idn'=>'int','idp'=>'int','bid'=>'int','txt'=>'text','rel'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function headers(){
	Head::add('csscode','.slide{background:black; color:white; align:center;
	display:flex; min-height:300px; width:100%; padding:100px; margin:10px 0 0 0;
	white-space:pre-wrap;}');}

static function titles($p){
	$d=val($p,'appMethod');
	$r['addslide']=lang('creating').' '.lang('slide');
	$r['mdfslide']=lang('editing').' '.lang('slide');
	$r['sldel']=lang('delete').' '.lang('slide');
	if(isset($r[$d]))return $r[$d];}

#sys
static function del($p){
	return appx::del($p);}

//static function save($p){return appx::save($p);}
static function save($p){$tit=val($p,'tit');
	$p['id']=Sql::insert(self::$db,['uid'=>ses('uid'),'tit'=>$tit]);
	Sql::insert('slide_page',array('1','0',$p['id'],lang('first slide'),'0'));
	return self::edit($p);}

static function modif($p){return appx::modif($p);}
static function create($p){return appx::create($p);}

#editor
static function form($p){return appx::form($p);}
static function edit($p){return appx::edit($p);}

//sysedit
static function sysedit($p){
	$id=val($p,'id'); $idn=val($p,'idn');
	$p['table']='slide_page';
	$p['cols']='idp,txt,rel';
	$p['colslabels']='parent id,content,related id';
	$p['act']='modif';
	$p['id']=Sql::read('id','slide_page','v','where bid="'.$id.'" and idn="'.$idn.'"');
	$ret=Edit::com($p);
return $ret;}

//del
static function sldel($p){
	$ok=val($p,'ok'); $delall=val($p,'delall'); $rid=val($p,'rid');
	$id=val($p,'id'); $idn=val($p,'idn'); if($idn==1)$idn=0;//forbid del first slide
	$prm='id='.$id.',idn='.$idn.',rid='.$rid; $cb=self::$cb.$id;
	if(!$ok){$prm=$cb.',,x|slide,sldel|'.$prm.',ok=1'; 
		if($delall)return aj($prm.',delall=1',langp('del all slides'),'btdel');
		else return aj($prm,langp('del').' '.lang('slide').': '.$idn,'btdel');}
	elseif($id && $delall){Sql::delete(self::$db,$id); Sql::delete('slide_page',$id,'bid'); 
		return self::stream($p);}
	elseif($id && $idn){//reorder slides and parents after deleting
		Sql::query('delete from slide_page where bid="'.$id.'" and idn="'.$idn.'"');
		$r=Sql::read('id,idn','slide_page','kv','where bid="'.$id.'" and idn>"'.$idn.'" order by idn');
		if($r)foreach($r as $k=>$v){$nidn=$v-1; Sql::update('slide_page','idn',$nidn,$k);}
		$r=Sql::read('id,idp','slide_page','kv','where bid="'.$id.'" and idp>="'.$idn.'" order by idn');
		if($r)foreach($r as $k=>$v)if($v){$nidp=$v-1; Sql::update('slide_page','idp',$nidp,$k);}
		$p['idn']=$idn-1>0?$idn-1:1; return self::play($p);}}

//add slide
static function lasidn($bid){
	$r=Sql::read('idn','slide_page','rv','where bid="'.$bid.'" order by idn');
	if($r)return max($r)+1; else return 1;}

static function addsav($p){$aid=val($p,'aid'); $mdf=val($p,'mdf');
	if($mdf)Sql::updates('slide_page',vals($p,['idp','txt','rel']),$aid);
	else $nid=Sql::insert('slide_page',vals($p,['idn','idp','bid','txt','rel']));
	return self::play($p);}

static function addslide($p){
	$rid=val($p,'rid'); $id=val($p,'id',1);
	$idp=val($p,'idn'); $idn=self::lasidn($id);//idp+1,idn+1
	$cb=self::$cb.$id; $cols='idp,txt,rel';
	$r=['idp'=>$idp,'txt'=>'','rel'=>''];
	$prm=$cb.',,x|slide,addsav|id='.$id.',bid='.$id.',idn='.$idn.',rid='.$rid;
	$ret=aj($prm.'|'.$cols,langp('save').' '.lang('slide').' '.$idn,'btsav');
	if($r)foreach($r as $k=>$v){
		if($k=='idp')$ret.=div(label($k,lang($k,1).span($v,'nfo')).hidden($k,$v));
		elseif($k=='txt')$ret.=div(textarea($k,$v,40,4).label($k,lang($k,1)));
		else $ret.=div(input($k,$v).label($k,lang($k,1)));}
	return $ret;}

static function mdfslide($p){
	$rid=val($p,'rid'); $id=val($p,'id',1);//id=bid
	$idn=val($p,'idn',1); $idn=val($p,'idn',1); $aid=val($p,'aid');//aid=id slide
	$cb=self::$cb.$id; $cols='idp,txt,rel';
	$r=Sql::read($cols,'slide_page','ra','where bid='.$id.' and idn='.$idn); //p($r);
	$prm=$cb.',,x|slide,addsav|'.'id='.$id.',idn='.$idn.',aid='.$aid.',rid='.$rid.',mdf=1|'.$cols;
	$ret=aj($prm,langp('modif').' '.lang('slide').' '.$idn,'btsav');
	if($r)foreach($r as $k=>$v){
		if($k=='txt')$ret.=div(textarea($k,$v,40,4).label($k,lang($k,1)));
		else $ret.=div(input($k,$v).label($k,lang($k,1)));}
	return $ret;}

//motor
static function build($r,$p){$ret=''; $bt=''; $next='';
	$tit=val($p,'tit'); $id=val($p,'id'); $idn=val($p,'inp',val($p,'idn',1)); 
	$rid=val($p,'rid'); $own=val($p,'own'); $cb=self::$cb.$id;
	if($r)foreach($r as $k=>$v)if($v['idn']==$idn)$ra=$v;
	if(isset($ra))$aid=$ra['id']; else $aid='';
	$prm='id='.$id.',rid='.$rid.',aid='.$aid.',idn='; $app='slide,play';
	$rb[]=aj($cb.'|slide,play|'.$prm.'1',ico('refresh'),'btn');
	if($own){
		//$bt.=aj($cb.'|slide,menu|'.$prm.$idn,langp('back'),'btn');
		//$bt.=aj('popup|slide,syseditit|'.$prm.$id,pic('edit'),'btn');
		$bt.=aj('popup|slide,addslide|'.$prm.$idn,langpi('add'),'btsav');
		$bt.=aj('popup|slide,mdfslide|'.$prm.$idn,langpi('modif'),'btn');
		$bt.=aj('popup|slide,sldel|'.$prm.$idn,langpi('del'),'btdel');
		$bt.=aj('popup|slide,sldel|'.$prm.$idn.',delall=1',langpi('delete'),'btdel');}
	$bt.=href('/slide/'.$id,pic('url'),1);
	$rb[]=span($bt,'right');
	if(isset($ra)){
		$p['id']=$ra['id']; $p['idn']=$idn;
		if($ra['idp'])
			$rb[]=aj($cb.'|'.$app.'|'.$prm.$ra['idp'],pic('previous').$ra['idp'],'btn');
		$rb[]=aj($cb.'|slide,play|'.$prm.$idn,icxt(ics('slide'),$idn),'btn');
		if($ra['rel'])$rb[]=aj($cb.'|'.$app.'|'.$prm.$ra['rel'],pic('parent'),'btn');
		if($r)foreach($r as $ka=>$va){
			if($va['rel']==$idn)
				$rb[]=aj($cb.'|'.$app.'|'.$prm.$va['idn'],pic('child'),'btn');
			if($va['idp']==$idn)
				$next.=aj($cb.'|'.$app.'|'.$prm.$va['idn'],pic('next').$va['idn'],'btn');
			$rb['nxt']=$next;}}
	$here=aj($cb.'|slide,play|'.$prm.$idn,icxt(ics('slide'),$idn),'btn').' ';
	if($rb)$bt=div(implode('',$rb));
	if(isset($ra))$ret=div($ra['txt'],'','tx'.$rid,'margin:auto;');//nl2br->white-space:pre-wrap;
	return $bt.div($ret,'slide');}

static function play($p){$id=val($p,'id');
	$r=Sql::read('id,idn,idp,txt,rel','slide_page','rr','where bid="'.$id.'" order by idn');
	if(!$r)return help('id not exists','paneb');
	$p['own']=Sql::read('id',self::$db,'v','where uid="'.ses('uid').'" and id="'.$id.'"');
	$p['tit']=Sql::read('tit',self::$db,'v',$id);
	$ret=div($p['tit'],'tit');
	return $ret.self::build($r,$p);}

static function stream($p){
	return appx::stream($p);}

#interfaces
static function tit($p){
	return appx::tit($p);}

//call (read)
static function call($p){$id=val($p,'id');
	return div(self::play($p),'',self::$cb.$id);}

//com (write)
static function com($p){
	return appx::com($p);}

//interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>