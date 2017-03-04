<?php

class Cms{
	
	static function edition($id){$ret='';
		$r=array('bold'=>ico('bold'),'italic'=>ico('italic'),'underline'=>ico('underline'),'insertUnorderedList'=>ico('list'),'Indent'=>ico('indent'),'Outdent'=>ico('outdent'));
		foreach($r as $k=>$v)$ret.=tag('button','onclick=document.execCommand(\''.$k.'\',false,null);',$v).'';
	return $ret;}

	static function com($p){
		$rid=randid('txt'); $j='';
		$ret=self::edition($rid);
		$prm=array('contenteditable'=>'true','id'=>$rid,'onkeydown'=>$j,'onclick'=>$j);
		$ret.=tag('div',$prm,val($p,'txt'));
		return $ret;
	}

}
?>