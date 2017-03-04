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

static function wysiwyg($id){
$r=array('bold'=>'bold','italic'=>'italic','underline'=>'underline','insertUnorderedList'=>'list-ul','insertOrderedList'=>'list-ol','Indent'=>'indent','Outdent'=>'outdent','JustifyLeft'=>'align-left','JustifyCenter'=>'align-center','createLink'=>'link','inserthorizontalrule'=>'minus');
foreach($r as $k=>$v)
	$ret[]=tag('button',array('onclick'=>'format(\''.$k.'\');'),ico($v,14));
return implode('',$ret);}

#content
static function content($p){$w=val($p,'pagewidth');
	$ret=hlpbt('pad_app').' ';
	//$prm=['id'=>'ckb','class'=>'btdel','onclick'=>'memStorage(\'txarea_m2_res_1\');',];
	//$ret.=tag('a',$prm,lang('restore')).' ';
	$prm=['id'=>'ckc','class'=>'btsav','onclick'=>'memStorage(\'txarea_m2_sav_1\');',];
	$ret.=tag('a',$prm,lang('save'));
	$ret.=self::wysiwyg('txarea');
	//$txt=Conn::load(['msg'=>val($p,'txt'),'ptag'=>1]);
	$txt=val($p,'txt');
	$ret.=tag('div',['contenteditable'=>'true','id'=>'txarea','class'=>'txth','style'=>'min-width:310px; min-height:140px;'],$txt);
	$ret.=Head::jsCode(self::injectJs());
	return $ret;}
}

?>