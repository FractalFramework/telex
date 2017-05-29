<?php
class _db{
static function save($p){
	$root=val($p,'root');
	$inp1=val($p,'inp1');
	if($inp1)Db::add($root,$inp1);
	return self::read($root);}

static function add($root){
	$ret=input('inp1','');
	$ret.=aj('dataTable|_db,save|root='.$root.'|inp1','Add','btn');
	return $ret;}

static function read($root){
	$datas=Db::read($root);
	return Build::table($datas);}

static function init($root){
	if(!is_file(Db::file($root))){
		$datas=array(1=>'one',2=>'two',3=>'three');
		Db::write($root,$datas);}}

static function content(){
	$root='one/two'; //Db::del($root);
	self::init($root);
	$datas=Db::read($root); //p($datas);
	$ret=tag('div','',self::add($root));
	$ret.=tag('div','id=dataTable',Build::table($datas));
	return $ret;}
}