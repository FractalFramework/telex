<?php

class admin_lang{
static $private='6';
static $db='lang';
static $ad='admin_lang';

static function headers(){
	Head::add('csscode','');}

//install
static function install(){
	Sql::create('lang',['ref'=>'var','voc'=>'var','app'=>'var','lang'=>'var']);}

static function equalize($p){
	$r=Sql::read('ref,lang,voc',self::$db,'kkv','where app="'.$p['app'].'"');
	$rb=array_keys($r);
	foreach($rb as $k=>$v)
		if(!isset($r[$v][$p['lang']])){$txt=''; $voc='';
			if($p['lang']!='en' && isset($r[$v]['en'])){$from='en'; $txt=$r[$v]['en'];}
			if($p['lang']!='fr' && isset($r[$v]['fr'])){$from='fr'; $txt=$r[$v]['fr'];}
			if($txt)$voc=Yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt],1);
			Sql::insert(self::$db,[$v,$voc,$p['app'],$p['lang']]);}
	return self::com($p);}

static function duplicates($p){
	$r=Sql::select('select id,ref,count(*) from lang where lang="'.$p['lang'].'" group by ref having count(*)>1','');
	return Build::table($r);}

//create new language
static function create($p){
	$newlng=val($p,'newlng'); $lng='fr';
	$ret=inp('newlng',$newlng);
	$ret.=aj('admlng|admin_lang,create||newlng',langp('add language'),'btn');
	if($newlng){
		$r=Sql::read('ref,voc,app',self::$db,'rr','where lang="'.$lng.'" limit 450,50');
		foreach($r as $k=>$v){
			$ex=Sql::read('voc',self::$db,'v','where ref="'.$v['ref'].'" and lang="'.$newlng.'"');
			if(!$ex){$v['lang']=$newlng;
				$v['voc']=Yandex::com(['from'=>$lng,'to'=>$newlng,'txt'=>$v['voc']],1);
				Sql::insert(self::$db,$v); $r[$k]=$v;}
			else $r[$k]['voc']=$ex;}
	$ret.=Build::table($r);}
	return $ret;}

static function translate($p){$voc=''; $txt=''; $copy=val($p,'copy');
	$r=Sql::read('lang,voc',self::$db,'kv',['ref'=>$p['ref']]);
	foreach($r as $k=>$v){
		if($p['lang']!='en' && isset($r['en'])){$from='en'; $txt=$r['en'];}
		if($p['lang']!='fr' && isset($r['fr'])){$from='fr'; $txt=$r['fr'];}}
	if($copy)$voc=utf8_decode(html_entity_decode($txt));
	elseif($txt)$voc=Yandex::com(['from'=>$from,'to'=>$p['lang'],'txt'=>$txt],1);
	//if($copy){$voc=utf8_decode($txt); Sql::update(self::$db,'txt',$txt,$copy);}
	//elseif($voc)self::insertup(['ref'=>$p['ref'],'txt'=>$txt,'lang'=>$p['lang']]);
	return $voc;}

//save
static function update($p){$rid=$p['rid'];
	Sql::update(self::$db,'voc',$p[$rid],$p['id']);
	Sql::update(self::$db,'app',$p['app'.$rid],$p['id']);
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::com($p);}

static function del($p){
	if($id=val($p,'id'))$nid=Sql::delete(self::$db,$id);
	if($ref=val($p,'ref'))$nid=Sql::delete(self::$db,$ref,'ref');
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::com($p);}//self::add($p).br().

static function save($p){
	$nid=Sql::insert(self::$db,array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::com($p);}

static function addfrom($p){
	$p['voc']=Yandex::com(['from'=>$p['from'],'to'=>$p['lang'],'txt'=>$p['fvoc']],1);
	$p['id']=Sql::insert(self::$db,array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::edit($p);}

static function edit($p){$rid=randid('voc');//id
	$r=Sql::read('ref,voc,lang,app',self::$db,'ra','where id='.$p['id']);
	$ret=label($rid,$r['ref'].' ('.$r['lang'].')').inp($rid,$r['voc'],16);
	$ro=Sql::read('distinct(app)',self::$db,'rv','');
	$ret.=datalist('app'.$rid,$ro,$r['app'],8,'app');
	$ret.=aj('admlng,,x|admin_lang,update|id='.$p['id'].',rid='.$rid.',lang='.$r['lang'].',app='.$r['app'].'|'.$rid.',app'.$rid,langp('save'),'btsav');
	$ret.=aj('input,'.$rid.'|admin_lang,translate|ref='.$r['ref'].',lang='.$r['lang'],langpi('translate'),'btn');
	$del='admlng,,x|admin_lang,del|lang='.$r['lang'].',app='.$r['app'];
	$ret.=aj($del.',id='.$p['id'],langpi('del'),'btdel');
	$ret.=aj($del.',ref='.$r['ref'],langpi('del all'),'btdel').br();
	foreach(Lang::$langs as $v)if($v!=$r['lang']){
		$id=Sql::read('id',self::$db,'v',['ref'=>$r['ref'],'lang'=>$v]);
		if($id)$ret.=aj('popup,,x|admin_lang,edit|id='.$id,$v,'btn');
		else $ret.=aj('popup,,x|admin_lang,addfrom|app='.$r['app'].',lang='.$v.',ref='.$r['ref'].',from='.$r['lang'].',fvoc='.$r['voc'],$v,'btsav');}
	return $ret;}

static function open($p){$ref=val($p,'ref'); $app=val($p,'app');
	$p['id']=Sql::read('id',self::$db,'v',['ref'=>$ref]);
	if(!$p['id'])$p['id']=Sql::insert(self::$db,[$ref,'',$app,ses('lng')]);
	if($p['id'])return self::edit($p);}

static function add($p){//ref,voc
	$ref=val($p,'ref'); $voc=val($p,'voc');
	$ret=inp('ref',$ref?$ref:'',16,'ref').inp('voc',$voc?$voc:'',16,'voc');
	$ret.=aj('admlng,,x|admin_lang,save||app,lang,ref,voc',langp('save'),'btsav');
	return $ret;}

//table
static function select($app,$lang){
	$ret=hidden('app',$app).hidden('lang',$lang);
	//langs
	$r=Sql::read('distinct(lang)',self::$db,'rv','');
	foreach($r as $v){$c=$v==$lang?' active':'';
		$rc[]=aj('admlng|admin_lang,com|lang='.$v.'|app',$v,'btn'.$c);}
	$bt=implode(' ',$rc).' :: ';
	//apps
	$r=Sql::read('distinct(app)',self::$db,'rv','order by app');
	if(!$r)$r=Lang::$langs;
	$rb[]=aj('admlng,,y|admin_lang,com|app=new|lang','new','btn'.($app=='new'?' active':''));
	$rb[]=aj('admlng,,y|admin_lang,com|app=all|lang','all','btn'.($app=='all'?' active':''));
	foreach($r as $v){$c=$v==$app?' active':'';
		$rb[]=aj('admlng,,y|admin_lang,com|app='.$v.'|lang',$v,'btn'.$c);}
	$bt.=implode(' ',$rb);
	$ret.=div($bt,'pane');
	if(ses('auth')>6){
		$ret.=aj('popup|admin_lang,add|app='.$app,langp('add'),'btn');
		$ret.=aj('admlng|admin_lang,equalize||app,lang',langp('equalize'),'btn');
		$ret.=aj('popup|Sql,mkbcp|b=lang',langp('backup'),'btsav');
		if(Sql::exists('lang_bak'))
		$ret.=aj('popup|Sql,rsbcp|b=lang',langp('restore'),'btdel');
		//$ret.=aj('admlng|admin_lang',langp('reload'),'btn').br();
		$ret.=aj('popup|admin_lang,duplicates|lang='.$lang,langp('duplicates'),'btn');
		$ret.=aj('admlng|admin_lang,create',langp('add language'),'btn');}
	return $ret;}

static function com($p){$rb=array();
	$app=val($p,'app','new'); $lang=val($p,'lang');
	$bt=self::select($app,$lang).br();
	if($app=='new')$wh=' and voc=""';
	elseif($app!='all')$wh=' and app="'.$app.'"'; else $wh='';
	$r=Sql::read('id,ref,voc',self::$db,'','where lang="'.$lang.'"'.$wh.' order by ref');
	$n=count($r);
	$bt.=span($n.' '.langs('occurence',$n,1),'small');
	foreach($r as $k=>$v){
		if(ses('auth')>6)$ref=aj('popup|admin_lang,edit|id='.$v[0],$v[1],'btn');
		else $ref=$v[1];
		if($v[2])$rb[$k]=array($ref,$v[2]);
		else $rc[$k]=array($ref,$v[2]);}
	if(isset($rc))$rb=array_merge($rc,$rb);
	array_unshift($rb,array('ref',$lang));
	return $bt.Build::table($rb,'','',1);}

//content
static function content($p){$ret='';
	//self::install();
	$app=val($p,'app',''); $lang=val($p,'lang',Lang::$lang);
	$ret=self::com(array('app'=>$app,'lang'=>$lang));
	return div($ret,'','admlng');}
}
?>