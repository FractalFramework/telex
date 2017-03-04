<?php

class Yandex{
//https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/static $private='1';

static function getkey(){
	if(!ses('yndxkek'))ses('yndxkek',File::read('cnfg/yandex.txt'));
	return ses('yndxkek');}

static function api($vr,$mode){
	$vr['key']=self::getkey();
	if(!$mode)$mode='translate';//detect//getLangs
	$u='https://translate.yandex.net/api/v1.5/tr.json/'.$mode.'?'.mkprm($vr);
	$d=File::read($u);
	$r=json_decode($d,true);
	//$r=json_dec($d);
	return $r;}

static function getlangs(){$rb=[];
	$r=self::api('','getLangs');
	foreach($r['dirs'] as $v)$rb=merge($rb,explode('-',$v));
	return implode(',',$rb);}

static function detect($p){
	$r=self::api(['text'=>val($p,'txt')],'detect');
	return $r['lang'];}

#reader
static function build($p){$id=val($p,'id'); $ret='';
	$txt=val($p,'txt','');
	$from=val($p,'from','');//use comma as separator
	$to=val($p,'to',ses('lng'));//default lang
	$format=val($p,'format','plain');//plain//html
	$options=val($p,'option','1');//1 for autodetect (empty) from
	if($from)$lang=$from.'-'.$to; else $lang=$to;
	$vr=['text'=>$txt,'lang'=>$lang,'format'=>$format,'options'=>$options];
	$r=self::api($vr,'translate');
	return $r;}

static function read($p){
	$r=self::build($p);
	$detected_lang=$r['detected']['lang'];
	$text=$r['text'][0];
	$text=decode($text);
	$ret=div(lang('detected_lang').' '.$detected_lang,'grey').div($text,'pane');
	return $ret;}
	
//com (apps)
static function com($p,$o=''){
	$r=self::build($p);
	$ret=$r['text'][0];
	if($o)$ret=utf8_decode($ret);
	return $ret;}
	
//interface
static function content($p){
	$rid=randid('yd');
	$p['txt']=val($p,'txt',val($p,'param'));
	$ret=input('txt',$p['txt']);
	$ret.=aj($rid.'|Yandex,read||txt',lang('translate'),'btn');
	//$ret.=aj('popup|Yandex,getlangs||txt',lang('lang'),'btn');
	$ret.=aj('popup|Yandex,detect||txt',lang('detect'),'btn');
	return $ret.div('','board',$rid);}
}
?>