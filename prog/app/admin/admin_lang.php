<?php

class admin_lang{
static $private='6';

static function headers(){
	Head::add('csscode','');}

/*static function admin(){
	$r[]=array('manage/admin','','admin_lang','monitor','admin_lang');
	$r[]=array('manage/admin','','admin_help','monitor','admin_help');
	$r[]=array('manage/admin','','admin_icons','monitor','admin_icons');
	return $r;}*/

//install
static function install(){
	Sql::create('lang',array('ref'=>'var','voc'=>'var','app'=>'var','lang'=>'var'));}

static function equalize($p){
	$r=Sql::read('ref,lang,voc','lang','kkv','where app="'.$p['app'].'"');
	$rb=array_keys($r);
	foreach($rb as $k=>$v)
		if(!isset($r[$v][$p['lang']])){
			$voc=isset($r[$v]['en'])?$r[$v]['en']:$v;
			$voc=Yandex::com(['from'=>'en','to'=>$p['lang'],'txt'=>$voc],1);
			if(!isset($r[$v][$p['lang']]))
				Sql::insert('lang',array($v,$voc,$p['app'],$p['lang']));}
	return self::com($p);}

static function duplicates($p){
	$r=Sql::select('select id,ref,count(*) from lang where lang="'.$p['lang'].'" group by ref having count(*)>1','');
	return Build::table($r);}

//create new language
static function create($p){
	$newlng=val($p,'newlng'); $lng='fr';
	$ret=input('newlng',$newlng);
	$ret.=aj('admlng|admin_lang,create||newlng',langp('add language'),'btn');
	if($newlng){
		$r=Sql::read('ref,voc,app','lang','rr','where lang="'.$lng.'" limit 450,50');
		foreach($r as $k=>$v){
			$ex=Sql::read('voc','lang','v','where ref="'.$v['ref'].'" and lang="'.$newlng.'"');
			if(!$ex){$v['lang']=$newlng;
				$v['voc']=Yandex::com(['from'=>$lng,'to'=>$newlng,'txt'=>$v['voc']],1);
				Sql::insert('lang',$v); $r[$k]=$v;}
			else $r[$k]['voc']=$ex;}
	$ret.=Build::table($r);}
	return $ret;}

//save
static function update($p){$rid=$p['rid'];
	Sql::update('lang','voc',$p[$rid],$p['id']);
	Sql::update('lang','app',$p['app'.$rid],$p['id']);
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::com($p);}

static function del($p){
	if($id=val($p,'id'))$nid=Sql::delete('lang',$id);
	if($ref=val($p,'ref'))$nid=Sql::delete('lang',$ref,'ref');
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::add($p).br().self::com($p);}

static function save($p){
	$nid=Sql::insert('lang',array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::add($p).br().self::com($p);}

static function addfrom($p){
	$p['voc']=Yandex::com(['from'=>$p['from'],'to'=>$p['lang'],'txt'=>$p['fvoc']],1);
	$p['id']=Sql::insert('lang',array($p['ref'],$p['voc'],$p['app'],$p['lang']));
	sesclass('Lang','com',$p['lang'],1);//update session
	return self::edit($p);}

static function edit($p){$rid=randid('voc');//id
	$r=Sql::read('ref,voc,lang,app','lang','ra','where id='.$p['id']);
	$ret=label($rid,$r['ref'].' ('.$r['lang'].')').input($rid,$r['voc'],16);
	$ro=Sql::read('distinct(app)','lang','rv','');
	$ret.=datalist($ro,'app'.$rid,$r['app'],8,'app');
	$ret.=aj('admlng,,x|admin_lang,update|id='.$p['id'].',rid='.$rid.',lang='.$r['lang'].',app='.$r['app'].'|'.$rid.',app'.$rid,langp('save'),'btsav');
	$del='admlng,,x|admin_lang,del|lang='.$r['lang'].',app='.$r['app'];
	$ret.=aj($del.',id='.$p['id'],langp('del'),'btdel');
	$ret.=aj($del.',ref='.$r['ref'],langp('del all'),'btdel').br();
	foreach(Lang::$langs as $v)if($v!=$r['lang']){
		$id=Sql::read('id','lang','v',['ref'=>$r['ref'],'lang'=>$v]);
		if($id)$ret.=aj('popup,,x|admin_lang,edit|id='.$id,$v,'btn');
		else $ret.=aj('popup,,x|admin_lang,addfrom|app='.$r['app'].',lang='.$v.',ref='.$r['ref'].',from='.$r['lang'].',fvoc='.$r['voc'],$v,'btsav');
	}
	return $ret;}

static function add($p){//ref,voc
	$ref=val($p,'ref'); $voc=val($p,'voc');
	$ret=input('ref',$ref?$ref:'',16,'ref').input('voc',$voc?$voc:'',16,'voc');
	$ret.=aj('admlng,,x|admin_lang,save||app,lang,ref,voc',langp('save'),'btsav');
	return $ret;}

//table
static function select($app,$lang){
	$ret=hidden('app',$app).hidden('lang',$lang);
	//langs
	$r=Sql::read('distinct(lang)','lang','rv','');
	foreach($r as $v){$c=$v==$lang?' active':'';
		$rc[]=aj('admlng|admin_lang,com|lang='.$v.'|app',$v,'btn'.$c);}
	$bt=implode(' ',$rc).' :: ';
	//apps
	$r=Sql::read('distinct(app)','lang','rv','order by app');
	if(!$r)$r=Lang::$langs;
	$c=$app=='all'?' active':'';
	$rb[]=aj('admlng,,y|admin_lang,com|app=all|lang','all','btn'.$c);
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
	$app=val($p,'app','all'); $lang=val($p,'lang');
	$bt=self::select($app,$lang).br();
	if($app!='all')$wh=' and app="'.$app.'" '; else $wh='';
	$r=Sql::read('id,ref,voc','lang','','where lang="'.$lang.'"'.$wh.' order by ref');
	$n=count($r);
	$bt.=span($n.' '.plurial('occurence',$n,1),'small');
	foreach($r as $k=>$v){
		if(ses('auth')>6)$ref=aj('popup|admin_lang,edit|id='.$v[0],$v[1],'btn');
		else $ref=$v[1];
		if($v[2])$rb[$k]=array($ref,$v[2]);
		else $rc[$k]=array($ref,$v[2]);}
	if(isset($rc))$rb=array_merge($rc,$rb);
	array_unshift($rb,array('ref',$lang));
	return $bt.Build::table($rb,'bkg');}

//content
static function content($p){$ret='';
	//self::install();
	$app=val($p,'app',''); $lang=val($p,'lang',Lang::$lang);
	$ret=self::com(array('app'=>$app,'lang'=>$lang));
	return div($ret,'','admlng');}

}

?>