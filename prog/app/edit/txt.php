<?php
class txt{
static function injectJs(){
	return '
	if(localStorage["m1"]!=undefined)
		document.getElementById("txarea").value=localStorage["m1"];';}

static function headers(){}

static function admin(){
	$r[]=['','pop','Help,com|ref=txt_app','','about'];
	$r[]=['editors','lk','txt','','txt'];
	$r[]=['editors','lk','pad','','pad'];
	$r[]=['editors','lk','convert','','convert'];
	return $r;}

static function popup(){
	$bt=aj('pagup|txt',ico('window-maximize'));
	$ret['title']='NotePad';
	$ret['wifth']=640;
	return $ret;}

#content
static function content($prm){$ret='';
	//$ret.=tag('a','id=ckb,class=btdel,onclick=memStorage(\'txarea_m1_res\');',lang('restore')).' ';
	$ret.=tag('a','id=ckc,class=btsav,onclick=memStorage(\'txarea_m1_sav\');',lang('save'));
	$inp=tag('textarea',array('id'=>'txarea','class'=>'editarea'),'');
	$ret.=div($inp);
	$ret.=Head::jscode(self::injectJs());
	return $ret;}
}
?>