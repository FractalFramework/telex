<?php

class pad{

static function injectJs(){
	return '
	function format(p,o){document.execCommand(p,false,o?o:null);}
	if(localStorage["m2"]!="undefined")
		document.getElementById("txarea").innerHTML=localStorage["m2"];';}

static function headers(){
	Head::add('csscode','.wrapper{max-width:600px; margin:0 auto;}');
	Head::add('jscode','');
	Head::add('jscode',self::injectJs());}

static function popup(){
	$bt=aj('pagup|pad',ico('window-maximize'));
	$ret['title']='NotePad';
	$ret['wifth']=640;
	return $ret;}

static function admin(){
	$r[]=['','pop','Help,com|ref=pad_app','','about'];
	$r[]=['editors','lk','txt','','txt'];
	$r[]=['editors','lk','pad','','pad'];
	$r[]=['editors','lk','convert','','convert'];
	return $r;}

#content
static function content($p){$w=val($p,'pagewidth'); $ret='';
	//$prm=['id'=>'ckb','class'=>'btdel','onclick'=>'memStorage(\'txarea_m2_res_1\');',];
	//$ret.=tag('a',$prm,lang('restore')).' ';
	$prm=['id'=>'ckc','class'=>'btsav','onclick'=>'memStorage(\'txarea_m2_sav_1\');',];
	$ret.=tag('a',$prm,lang('save'));
	$ret.=Build::wysiwyg('editarea');
	//$txt=Conn::load(['msg'=>val($p,'txt'),'ptag'=>1]);
	$txt=val($p,'txt');
	$ret.=tag('div',['contenteditable'=>'true','id'=>'txarea','class'=>'editarea','style'=>'min-width:310px; min-height:140px;'],$txt);
	$ret.=Head::jscode(self::injectJs());
	return $ret;}
}

?>