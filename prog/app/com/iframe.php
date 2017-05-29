<?php

class iframe{
	
static function get($p){
	$f=val($p,'url'); $f=http($f);
	$w=val($p,'pagewidth','620')-40;
	if($f)return iframe($f,$w,$w*0.6);}

static function content($p){$ret='';
	$rid=randid('ifr'); $f=val($p,'url');
	$bt=input('url',$f,32).' ';
	$bt.=aj($rid.',,y|iframe,get||url','ok','btn');
	if($f)$ret=self::get($f);
	return $bt.div($ret,'',$rid);}
}
?>
