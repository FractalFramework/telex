<?php

class Head{
static $add;

static function headerHtml(){
	return '<!DOCTYPE html>
<html lang="fr" xml:lang="fr">';}

static function meta($attr,$prop,$content=''){
	return '<meta '.$attr.'="'.$prop.'"'.($content?' content="'.$content.'"':'').'>';}

static function cssLink($u){
	if(strrchr($u,'.')=='.css')
		return '<link href="/'.ses('dev').$u.'" rel="stylesheet" type="text/css">';}

static function jsLink($u){
	if(substr($u,0,4)!='http')$root='/'.ses('dev'); else $root='';
	return '<script src="'.$root.$u.'"></script>';}

static function cssCode($code){
	return '<style type="text/css">'.$code.'</style>';}

static function jsCode($code){
	return '<script type="text/javascript">'.$code.'</script>';}

static function add($action,$r){
	self::$add[][$action]=$r;}

static function add_prop($p,$v){
	self::$add[]['meta']=['attr'=>'property','prop'=>$p,'content'=>$v];}

static function add_name($p,$v){
	self::$add[]['meta']=['attr'=>'name','prop'=>$p,'content'=>$v];}

static function build(){$ret='';
	$r=self::$add;
	if($r){
		foreach($r as $k=>$v){		
			if(is_array($v))$va=current($v);
			switch(key($v)){
				case('code'):$ret.=$va."\n"; break;
				case('csslink'):$ret.=self::cssLink($va)."\n"; break;
				case('jslink'):$ret.=self::jsLink($va)."\n"; break;
				case('csscode'):$ret.=self::cssCode($va)."\n"; break;
				case('jscode'):$ret.=self::jsCode($va)."\n"; break;
				case('rel'):$ret.='<link rel="'.$v['rel']['name'].'" href="'.$v['rel']['value'].'">'."\n"; break;
				case('meta'):$ret.=self::meta($v['meta']['attr'],$v['meta']['prop'],$v['meta']['content'])."\n"; break;
				case('tag'):$ret.=tag($v['tag'][0],$v['tag'][1],$v['tag'][2]); break;}}
		return $ret;}}

static function generate(){
	return self::headerHtml().tag('head','',self::build());}

}