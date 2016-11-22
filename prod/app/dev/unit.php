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
		$p['p2']=val($p,'inp2','test');
		$p['p3']=val($p,'inp3');
		$ret=input_label('inp1',$p['p1'],'app').br();
		$ret.=input_label('inp2',$p['p2'],'method').br();
		$ret.=input_label('inp3',$p['p3'],'param1=p1,...').br();
		$ret.=ajax($p['rid'],'unit,build','headers=1,injectJs=1','inp1,inp2,inp3',lang('ok'),'btn');
		return $ret.div('','deco',$p['rid']);
	}
}
?>
