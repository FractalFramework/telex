<?php

class keygen{

	static function build($prm){
		$p=val($prm,'length',8); $o=val($prm,'cmpx',0); 
		$a='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMONPQRSTUVWXYZ0123456789';
		if($o==1)$a.='$£%*µ,?;.:/!&#{[-|_)]=} ';
		$r=str_split($a); $n=count($r)-1; $ret='';
		for($i=0;$i<$p;$i++)$ret.=$r[rand(0,$n)];
	return $ret;}
	
	static function content($prm){
		//$p1=val($prm,'p1');
		$ret=input('length','10','');
		$ret.=checkbox('complex',array('cmpx'=>'more complex'));
		$ret.=Ajax('gnpw','keygen,build','','length,cmpx',lang('ok',1),'btn').' ';
		$ret.=hlpbt('keygen');
		return $ret.div('','deco','gnpw');
	}
}
?>