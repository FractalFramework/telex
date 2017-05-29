<?php

class op{	
static $private='6';

static function injectJs(){
	return '';}

static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#build
static function build($p){
	return $r;}

#read
static function call($p){
	$r=Sql::read('*','profile','rr',''); //p($r);
	//$ra=Sql::columns('profile',0); pr($ra);
	foreach($r as $k=>$v){
		$v['id']=$v['puid'];
		$sql='insert into profile2 values ';
		$sql.=Sql::insert_from_array($v,1);
		//$rq=Sql::query($sql,'');
		echo $sql.br();
		}
	//Sql::insert2('profile',$rb,'1','1');
	return $p['msg'].': '.$p['inp1'];}

static function com(){
	return self::content($p);}

#content
static function content($p){
	$p['p1']=val($p,'param',val($p,'p1'));
	$ret=input('inp1','value1','','1');
	$ret.=aj('wrp|op,call|msg=text|inp1',lang('send'),'btn');
return $ret.div('','','wrp');}
}
?>