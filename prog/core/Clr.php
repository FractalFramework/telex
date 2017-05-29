<?php
class Clr{
static function get(){
	return Db::read('system/colors');}
static function read($d){
	$r=sesclass('Clr','get','',0);
	if(isset($r[$d]))return $r[$d];}
static function random(){
	$r=sesclass('Clr','get','',0);
	$r=array_values($r);
	return $r[rand(0,139)];}
}
?>