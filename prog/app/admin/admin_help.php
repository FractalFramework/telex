<?php

class admin_help{
	static $private='6';

	static function headers(){
		Head::add('csscode','');}
	
	//install
	static function install(){
		Sql::create('help',array('ref'=>'var','txt'=>'text','lang'=>'var'),0);}
	
	static function equilibrium($p){
		$r=Sql::read('ref,lang,txt','help','kkv','');
		$rb=array_keys($r);
		foreach($rb as $k=>$v)
			if(!isset($r[$v][$p['lang']]))
				Sql::insert('help',array($v,isset($r[$v]['en'])?$r[$v]['en']:'',$p['lang']));
		return self::com($p);}
	
	//save
	static function update($p){$rid=$p['rid'];
		Sql::update('help','txt',$p[$rid],$p['id']);
		return self::com($p);}
	
	static function del($p){
		$nid=Sql::delete('help',$p['id']);
		return self::com($p);}
	
	static function save($p){
		$nid=Sql::insert('help',array($p['ref'],$p['txt'],$p['lang']));
		return self::com($p);}
	
	static function edit($p){$rid=randid('help');
		$to=val($p,'to')?'socket,,x':'admhlp,,x';
		$r=Sql::read('ref,txt,lang','help','ra','where id='.$p['id']);
		$ret=label($rid,$r['ref'].' ('.$r['lang'].')');
		$ret.=aj($to.'|admin_help,update|id='.$p['id'].',rid='.$rid.',lang='.$r['lang'].'|'.$rid,lang('save'),'btsav');
		$ret.=aj($to.'|admin_help,del|id='.$p['id'].',lang='.$r['lang'],lang('del'),'btdel');
		$lgb=$r['lang']=='fr'?'en':'fr';
		$ret.=aj('popup,,x|admin_help|lang='.$lgb,pico('language'),'btn').br();
		$ret.=goodinput($rid,$r['txt']);
		return $ret;}
	
	static function add($p){//ref,txt
		$ref=val($p,'ref'); $txt=val($p,'txt');
		$ret=input('ref',$ref?$ref:'',16,'ref').input('txt',$txt?$txt:'',36,'help');
		$ret.=aj('admhlp,,x|admin_help,save||lang,ref,txt',lang('save'),'btn');
		return $ret;}
	
	//table
	static function select($lang){
		$ret=hidden('lang',$lang);
		//$r=Sql::read('distinct(lang)','help','rv','');
		$r=Lang::$langs;
		foreach($r as $v){$c=$v==$lang?' active':'';
			$rc[]=aj('admhlp|admin_help,com|lang='.$v,$v,'btn'.$c);}
		$ret.=div(implode('',$rc),'pane').br();
		if(ses('auth')>6){
			$ret.=aj('popup|admin_help,add',lang('add'),'btn');
			$ret.=aj('admhlp|admin_help,equilibrium||lang',lang('update'),'btn');
			$ret.=aj('popup|Sql,mkbcp|b=help',lang('backup'),'btsav');
			if(Sql::exists('help_bak'))
			$ret.=aj('popup|Sql,rsbcp|b=lang',lang('restore'),'btdel').br();}
		return $ret;}
	
	static function com($p){$rb=array();
		$lang=val($p,'lang');
		$bt=self::select($lang).br();
		$r=Sql::read('id,ref,txt','help','','where lang="'.$lang.'"');
		if($r)foreach($r as $k=>$v){$v[2]=nl2br($v[2]);
			if(ses('auth')>6)$ref=aj('popup|admin_help,edit|id='.$v[0],$v[1],'btn');
			else $ref=$v[1];
			if($v[2])$rb[$k]=array($ref,$v[2]);
			else $rc[$k]=array($ref,$v[2]);}
		if(isset($rc))$rb=merge($rc,$rb);
		array_unshift($rb,array('ref',$lang));
		return $bt.Build::table($rb,'bkg');}
	
	//content
	static function content($p){$ret='';
		//self::install();
		$lang=val($p,'lang',Lang::$lang);
		$ret=self::com(array('lang'=>$lang));
		return div($ret,'','admhlp');}

}

?>