<?php

class unit{
	static $private='6';
	
	static function locate($d){
		$r=sesfunc('scandir','app');
		if($r)foreach($r as $k=>$v)
			if(file_exists('core/'.$v.'/'.$d.'.php'))return 'core/'.$v.'/'.$d.'.php';
			elseif(file_exists('app/'.$v.'/'.$d.'.php'))return 'app/'.$v.'/'.$d.'.php';}
	
	static function view($app,$mth){
		$f=self::locate($app);
		$d=File::read($f);
		return nl2br(innerfunc($d,$mth));
	}
	
	static function build($p){
		$p1=val($p,'inp1'); $p2=val($p,'inp2'); $p3=val($p,'inp3');
		if(method_exists($p1,$p2))$ret=($p1::$p2(atbr($p3)));
		else $ret=call_user_func($p1,$p3);
		$ret.=br();
		$ret.=div(tag('pre','',htmlentities($ret)),'console').br();
		$ret.=div(self::view($p1,$p2),'console');
		return $ret;
	}
	
	//unitary tests
	static function content($p){
		$p['rid']=randid('md');
		$p['p1']=val($p,'inp1','Vue');
		$p['p2']=val($p,'inp2','call');
		$p['p3']=val($p,'inp3','p1=v1,p2...');
		$ret=input_label('inp1',$p['p1'],'app').br();
		$ret.=input_label('inp2',$p['p2'],'method').br();
		$ret.=textarea('inp3',$p['p3'],40,4,'','console').br();
		$ret.=aj($p['rid'].'|unit,build|headers=1,injectJs=1|inp1,inp2,inp3',lang('ok',1),'btn');
		return $ret.div('','',$p['rid']);
	}
}
?>
