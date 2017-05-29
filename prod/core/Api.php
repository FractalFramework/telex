<?php
class Api{
static function build($r){
	return json_encode($r);}
//tlex
static function call($p){
	return tlex::call($p);}
static function post($p){
	$msg=get('msg');}
static function content($p){
	$app=val($p,'app'); ses('app',$app);
	$ret=Menu::call(array('app'=>'Api','method'=>'menus','css'=>'fix'));
	return $ret;}
}
?>