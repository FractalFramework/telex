<?php
class install{
//while installation, set private=0;
	static $private=6;
	
	static function menubt($dr,$f){
		$app=before($f,'.');
		if(method_exists($app,'install')){
			$app::install();
			return $f.br();}}
	
	static function build($p){
		$r=Dir::walk('install','menubt','prod');
		return implode('',$r);}
	
	static function json($p){
		$table=val($p,'inp1','');
		$r=Sql::read('*',$table,'rr','');
		return json_encode($r);}
	
	static function content($p){
		$p['rid']=randid('md');
		$bt=hlpbt('install').' ';
		$bt.=aj($p['rid'].'|install,build',langp('install'),'btn');
		$bt.=aj($p['rid'].'|apisql',langp('databases'),'btn');
		$bt.=href('/app/update',langp('updates'),'btn');
		return $bt.div('','',$p['rid']);}
}
?>
