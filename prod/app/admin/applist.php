<?php

class applist{
public $ret;

static function headers(){
	Head::add('csscode','
	.block{border:1px solid grey; border-radius:2px; background:white;
	padding:10px; margin:10px 0;}
	.block a:hover{text-decoration:underline;}
	.block span{display:block; cursor:auto;}
	.block span:hover{background:white;}
	.block div{}');}

static function comdir(){
	$dirs=Dir::read(ses('dev').'/app');
	foreach($dirs as $dir=>$files){
		if(is_array($files) && $dir)foreach($files as $k=>$file){
			if(is_string($file))$app=before($file,'.');
			if($app)$private=isset($app::$private)?$app::$private:0;
			$dr='Apps/'.$dir;
			if(!$private or ses('auth')>=$private)
				$r[]=array($dr,'pop',$app.'|headers=1','',$app);}}
	return $r;}

static function appdir($dir,$files){$ret='';
	foreach($files as $k=>$v){
		if(!is_array($v))list($app,$ext)=explode('.',$v);
		$private=isset($app::$private)?$app::$private:0;
		if(!$private or ses('auth')>=$private)
			$ret.=popup($app.'|headers=1',langp($app),'');
	}
	if($ret)return div(div($dir),'block').div($ret,'list');}

static function tlex(){$ret='';
	$r=Sql::read('com','desktop','rv','where dir like "/apps/tlex%" and auth<=2');
	$bt=tag('h1','',lang('applist'));
	if($r)foreach($r as $k=>$v){$va=after($v,'/');
		$ret.=div(tag('h3','',pic($va,32).hlpxt($va)).hlpxt($va.'_app','board'));}
	return $bt.div($ret,'board');}

static function content($prm){$ret='';
	if(isset($prm['iframe']))$mod=$prm['iframe'];
	else $mod=get('app');	
	$r=Dir::read(ses('dev').'/app');
	if(isset($r)){
		foreach($r as $k=>$v){
			if(is_array($v))$ret.=self::appdir($k,$v);
			else $rb[$k]=$v;}}
	if(isset($rb))$first=self::appdir('root',$rb);
	else $first='';
	return $first.$ret;}
	
}

?>