<?php

class model{
	static $private='1';
	static $db='model';
	
	static function injectJs(){
		return '';}
		
	static function headers(){
		Head::add('csscode','');
		Head::add('jscode',self::injectJs());}
		
	static function admin(){
		$r[]=array('','j','popup|model,content','plus',lang('open'));
		return $r;}
		
	static function install(){
		Sql::create(self::$db,array('muid'=>'int','mname'=>'var'),1);}
		
	static function titles($p){
		$d=val($p,'appMethod');
		$r['content']='welcome';
		$r['build']='model';
		if(isset($r[$d]))return lang($r[$d]);}
	
	//edit
	static function edit(){
		$ret=Form::com(['table'=>self::$db]);}
	
	//builder
	static function build($p){
		$msg=val($p,'msg');
		$content=val($p,'inp1','nothing');
		return $msg.': '.$content;}
	
	static function menu($p){$fid=val($p,'fid'); $rid=val($p,'rid'); $xid=val($p,'xid');
		$ret=aj($p['rid'].'|model,edit|rid='.$rid,langp('add'),'btsav');//add
		$r=Sql::read('*',self::$db,'rr','where muid='.ses('uid'));
		$tmp='[[_ptit _bt _insert*class=tit:div][_ptxt*class=txt:div]*class=menu:div]';
		if($r)foreach($r as $k=>$v){
			$v['ptit']=aj('popup|petition,read|fid='.$v['id'],$v['ptit']);
			$v['bt']=aj($rid.'|petition,edit_lead|fid='.$v['id'].',rid='.$rid,pico('edit'));//edit
			if($xid)$v['insert']=insertbt(lang('use'),$v['id'].':petition',$xid); else $v['insert']='';
			$ret.=Vue::read($v,$tmp);}
		return $ret;}
	
	//call (connectors)
	static function call($p){
		return self::build($p);}
	
	//com (telex apps)
	static function tit($p){$id=val($p,'id');
		return Sql::read('mname',self::$db,'v','where id='.$id);}
	
	static function call($p){$id=val($p,'id');
		return self::menu($p);}
	
	//interface
	static function content($p){
		//self::install();
		$p['rid']=randid('md');
		$p['p1']=val($p,'param',val($p,'p1'));
		//$ret=hlpbt('model');
		$ret=input('inp1',$p['p1'],'10',1);
		$ret.=aj('popup|model,build|msg=text|inp1',lang('send'),'btn');
		return div($ret,'deco',$p['rid']);}
}
?>
