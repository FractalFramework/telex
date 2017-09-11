<?php
class Api{
static function build($r){
	return json_encode($r);}
//tlex
static function call($p){
	return tlex::call($p);}

static function read($p){
	Head::add_name('viewport','user-scalable=no, initial-scale=1, width=device-width');
	Head::add('csslink','/css/global.css');
	Head::add('csslink','/css/apps.css');
	Head::add('csslink','/css/tlex.css');
	Head::add('csslink','/css/fa.css');
	Head::add('csscode','body{margin:0;}');
	Head::add('jslink','/js/ajax.js');
	Head::add('jslink','/js/utils.js');
	$ret=Head::generate();
	$ret.='<body onmousemove="popslide(event)" onmouseup="closebub(event)">'."\n";//
	$ret.=div(encode(tlxcall::one($p)),'pane',$p['id']);
	$ret.=div('','','popup');
	return $ret.'</body>';}

static function post($p){
	$msg=get('msg');}

static function content($p){
	$app=val($p,'app'); ses('app',$app);
	$ret=Menu::call(array('app'=>'Api','method'=>'menus','css'=>'fix'));
	return $ret;}
}
?>