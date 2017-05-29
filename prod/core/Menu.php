<?php

class Menu{	
#sample
static function menus(){
	//array('folder','/j/lk/in/t','app/action','picto','text')//use syslang, not lang()
	$r[]=array('menu1','j','popup|txt','text','textpad');
	return $r;}

#build button
static function bub($r){
	list($dir,$type,$call,$picto,$btn)=$r;
	if($picto)$pic=Icon::ex($picto)?ics($picto):$picto;
	elseif(Icon::ex($btn))$pic=ics($btn);
	elseif($type=='lk')$pic='link-intact'; 
	elseif($type=='j')$pic='check';
	else $pic='square-o';
	if(Lang::ex($btn))$btn=lang($btn);
	elseif($btn=='-')$btn='';
	$ico=$pic!='-'?ico($pic):'';//&&$dir
	$btn=span($ico,$dir?'ico':'').' '.span($btn);
	$attr['onclick']='bubClose();';
	$attr['onmouseover']='bubCloseOthers(this.parentNode);';
	if($type=='')$ret=popup($call.'|headers=1',$btn,'',$attr);
	elseif($type=='j')$ret=aj($call,$btn,'',$attr);
	elseif($type=='pag')$ret=pagup($call,$btn,'',$attr);
	elseif($type=='pop')$ret=popup($call,$btn,'',$attr);
	elseif($type=='img')$ret=imgup('img/full/'.$call,$btn);
	elseif($type=='in'){
		if(strpos($call,'|')){list($call,$prm)=explode('|',$call);
			$p=stringToArray($prm,',','=');}
		if(strpos($call,','))list($call,$mth)=explode(',',$call);
		if(isset($mth))$p['appMethod']=$mth; $p['headers']=1;
		$ret=tag('div','',App::open($call,$p));}
	elseif($type=='lk')$ret=href($call,$btn);
	elseif($type=='lkt')$ret=href($call,$btn,'',1);
	elseif($type=='t')$ret=tag('span','',$btn);
	else $ret=popup($call,$btn,'',$attr);
	return $ret;}

#build auto-button for sub-folders
static function subub($btn,$dir,$app,$mth,$drop){$id=randid('bub');
	$mode=strpos($dir,'/')===false?'1':'';//vertical bubble for the first level
	if($btn===ses('user'))$ico=ico('user-circle-o');//!
	elseif($ic=Icon::ex($btn))$ico=ico($ic);
	else{$ico=ico('chevron-right');}
	if(Lang::ex($btn))$btn=lang($btn);
	if($mode)$btn=span($btn,'react');
	if(!$mode)$ico=span($ico,'ico').' '; else $ico=$ico.' ';
	$btn=$ico.span($btn);
	if($drop)$call='drop,'.$id.','.$mode.'|Menu,call';
	else $call='menu,'.$id.','.$mode.'|Menu,call';
	$p='app='.$app.',method='.$mth.',dir='.$dir.',drop='.$drop;
	//$attr['onmouseover']='bubCloseTimer();';
	$attr['onmouseover']='ajaxTimer(\''.$call.'\',\''.$p.'\'); zindex(\''.$id.'\');';
	$attr['onmouseout']='clearTimeout(xc);';//clearTimeout(xb);
	if($drop)$attr='';
	$attr['id']=$id;
	$attr['onmousedown']='ajbt(this);'; $attr['data-j']=$call.'|'.$p;
	$ret=tag('a',$attr,$btn);
	return $ret;}

#displayed part of the master array $r
//root begin without '/' mean first level, mean vertical drop
static function build($r,$dir,$app,$mth,$drop){
	$rdir=explode('/',$dir);
	//$current_depht=count($rdir); 
	$current_depht=substr_count($dir,'/');
	$current_level=$rdir[$current_depht];
	foreach($r as $v){
		$level=explode('/',$v[0]); $depht=count($level)-1;
		//active_level: [1]/2/3
		if(array_key_exists($current_depht,$level))
			$active_level=$level[$current_depht];
		else $active_level='';
		//next_level: 1/[2]/3
		if(array_key_exists($current_depht+1,$level))
			$next_level=$level[$current_depht+1];
		else $next_level='';
		//first_levels: [1/2]/3
		$fsvl=array_slice($level,0,$current_depht+1);
		$first_levels=implode('/',$fsvl);
		//button
		if($v[0]==$dir)$ret[]=self::bub($v);
		//acceed to next level (second iteration)
		elseif($active_level==$current_level && $next_level)
			$ret[$next_level]=self::subub($next_level,$first_levels.'/'.$next_level,$app,$mth,$drop);
		//display next level (first iteration)
		elseif(substr($v[0],0,strlen($dir))==$dir && $depht>=$current_depht){
			$next=$next_level?$first_levels.'/'.$next_level:$active_level;
			$next=$active_level;
			$ret[$active_level]=self::subub($active_level,$next,$app,$mth,$drop);}
	}
	if(isset($ret))return implode('',$ret);}

#call
static function call($p){
	$dir=val($p,'dir'); $app=val($p,'app'); $drop=val($p,'drop');
	$mth=val($p,'method'); $css=val($p,'css'); $rid=val($p,'rid');
	if(method_exists($app,$mth)){$q=new $app; $r=$app::$mth($rid);}//['rid'=>$rid]
	if(isset($r))$bubble=self::build($r,$dir,$app,$mth,$drop);
	else $bubble='';//no datas found
	$attr['class']='bub'; $attr['onclick']='popz++; this.style.zIndex=popz;';
	if(!$dir && $css)$attr['class'].=' '.$css;
	if($dir)$attr['class'].=' ablock';//css for sublevels
	return tag('div',$attr,$bubble);
return $ret;}
}