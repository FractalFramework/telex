<?php
class Help{
	static function get($prm){$lg=ses('lng')?ses('lng'):self::$lang; $bt='';
		$r=Sql::read('id,txt','help','rw','where ref="'.$prm['ref'].'" and lang="'.$lg.'"');
		if(!$r[0] && $prm['ref'])$r[0]=Sql::insert('help',array($prm['ref'],'',$lg));
		if(auth(6))$bt=aj('popup|admin_help,edit|to=hlpxd,id='.$r[0].',headers=1',pic('edit')).' ';
		return span($bt.(isset($r[1])?nl2br($r[1]):$prm['ref']),val($prm,'css'),'hlpxd');
	}
	static function com($p){return div(self::get($p),'helpxt');}
}