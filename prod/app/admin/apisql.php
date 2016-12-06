<?php

class apisql{
	static $private=6;
	static $server='tlex.fr';
	
	static function admin(){
		$r[]=array('','j','popup|apisql,content','plus',lang('open'));
		return $r;
	}
	
	static function build($p){
		$table=val($p,'app','');
		$r=Sql::read('*',$table,'rr','');
		return Api::build($r);
	}
	
	static function call($p){
		$p=val($p,'app','');
		$f='http://'.self::$server.'/api.php?app=apisql&mth=render&p='.$p;
		$d=File::get($f);
		//$d=utf8_decode($d);
		if($d)$r=json_decode($d,true); //pr($r);
		if($_SERVER['HTTP_HOST']!=self::$server)
		if(isset($r) && is_array($r)){Sql::insert2($p,$r,1,0); return 'merge '.$p.' ok';}
		else return 'nothing'.Json::error();
	}
	
	static function render($table){
		$keys=Sql::columns($table,3); //echo $keys;
		$r=Sql::read($keys,$table,'rr','',0); //pr($r);
		//$r=utf8_r($r);//pr($r);
		//$ret=json_encode($r);//,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE
		//echo Json::error();
		$ret=json_r($r);//if(auth(6))
		//$ret=urlencode($ret);
		return $ret;
	}
	
	static function menu($p){//system tables
		$r=array('lang'=>'admin_lang','icons'=>'admin_icons','help'=>'admin_help','labels'=>'labels');//,'desktop'=>'desktop'
		foreach($r as $k=>$v){
			$app=!is_numeric($k)?$k:$v;
			if($k!='login')$ret[]=aj($p['rid'].'|apisql,call|app='.$app,$app,'btn');
		}
		return implode('',$ret);
	}
	
	static function content($p){
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));//unamed param before
		$bt=hlpbt('apisql');
		//$bt=input('app','value1',$p['p1'],'1');
		//$bt.=aj($p['rid'].'|apisql,call||app',lang('import'),'btsav');
		$bt.=self::menu($p);
		return $bt.div('','',$p['rid']);
	}
}
?>