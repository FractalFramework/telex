<?php
class Help{
static function read($ref){
	$lg=ses('lng')?ses('lng'):'fr';//(property_exists('parent','lang')?parent::$lang:)
	return Sql::read('txt','help','v','where ref="'.$ref.'" and lang="'.$lg.'"');}
static function get($p){$lg=ses('lng')?ses('lng'):self::$lang; $bt='';
	$r=Sql::read('id,txt','help','rw','where ref="'.$p['ref'].'" and lang="'.$lg.'"');
	if(!$r[0] && $p['ref'])$r[0]=Sql::insert('help',array($p['ref'],'',$lg));
	if(auth(6))$bt=aj('popup|admin_help,edit|to=hlpxd,id='.$r[0].',headers=1',ico('edit')).' ';
	if(isset($r[1]))$txt=val($p,'conn')?Conn::call($r[1]):nl2br($r[1]); else $txt=$p['ref'];
	if(val($p,'brut'))return $txt; else return div($bt.$txt,val($p,'css'),'hlpxd');}
static function conn($p){return div(self::get($p),'helpxt');}
static function com($p){return div(self::get($p),'helpxt');}
}