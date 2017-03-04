<?php
//trash

#1702

//global.css
/*@font-face{font-family:'DIGITALDREAM'; src:url('/fonts/DIGITALDREAM.eot?iefix') format('eot'),url('/fonts/DIGITALDREAM.woff') format('woff'),url('/fonts/DIGITALDREAM.svg') format('svg');}*/

//Upload
/*static function call(){
	return '<form id="upl" action="" method="POST" onchange="upload(1)">
	<label class="uplabel"><input type="file" id="upfile" name="upfile" multiple />
	'.ico('upload').'</label></form>'.div('','','upbck');}*/

//update
	/*static function loaddl(){$rb=[];
		//select recents
		$rid=randid('dl');
		$r=self::files2dl(); //pr($r);
		$d=implode('|',$r);
		//method1:tar file
		$f='http://'.self::$servr.'/api.php?app=update&mth=builddl&files='.$d.'&id='.$rid;
		//echo $d=File::read('http://'.self::$servr.'/'.$f);
		$fb='usr/dl/ffw.tar';
		//if($d)File::write($fb.'.gz',$d);
		//$d=File::readgz($fb.'.gz');
		//File::write($fb,$d);
		//unlink($fb.'.gz');
		
		//method2:oneByone
		if($r)foreach($r as $k=>$v){
			$f='http://'.self::$servr.'/api.php?app=update&mth=dlfile&file='.$v;
			$d=File::read($f);
			//$d=gzread($d,10000);
			File::write($v,$d);
		}*/
	
	#create gz (server)
	/*static function creategz(){
		Dir::mkdir_r('usr/dl');
		$local=self::localfdates();
		$r=self::mk_r($local);
		foreach($r as $f=>$dt){$ok=1;
			$fb='usr/dl/'.str_replace('/','-',$f).'.txt';
			$gz=File::day($fb);
			if($dt>$gz)$ok=File::write($fb,implode('',file($f)));
			if(!$ok)$ret[]='ok: '.$f.':'.($dt-$gz);
			else $ret[]='no: '.$f;}
	return implode(br(),$ret);}*/

//telex
#pub
/*static function pub(){
$r=['newsnet','socialsys','socialgov'];
foreach($r as $v)$ret[]=self::profile(['usr'=>$v,'small'=>'1']);
return implode('',$ret);}*/

//Build
/*static function scroll($r,$d,$n,$h='',$w=''){
	$max=is_numeric($r)?$r:count($r);
	$style='overflow-y:scroll; max-height:'.($h?$h:400).'px;'.($w?' min-width:'.$w.'px;':'');
	if($max>$n)return tag('div',array('id'=>'scroll','style'=>$style),$d); 
	else return $d;}*/

#deco
/*static function offon($state){
	return ico($state?'toggle-on':'toggle-off');}*/

//gps
/*	static function example(){
		$p['lat']=48.8390804;
		$p['lon']=2.23537670;
	}*/
		//$url=$host.'search/?q=8 bd du port&limit=15';
		//$url=$host.'search/?q=8 bd du port&lat=48.789&lon=2.789';
		//$url=$host.'search/?q=8 bd du port&postcode=44380';
		//$url=$host.'search/?q=paris&type=street';
		//$url=$host.'reverse/?lat=48.8390804&lon=2.23537670&type=street';
///api
		//$d=utf8_decode($d);
		//echo Json::error();
	//	$d=mb_convert_encoding($d,'UCS-2BE','UTF-8');
		//$d=utf8_decode($d);
		//$d=self::json_utf($d);
		//$d=self::unicode2html($d);
	
	/*
		[street] => Rue de Châteaudun
		[label] => 1 TER Rue de Châteaudun 92100 Boulogne-Billancourt
		[distance] => 7
		[context] => 92, Hauts-de-Seine, Île-de-France
		[id] => 92012_1430_187b83
		[postcode] => 92100
		[citycode] => 92012
		[name] => 1 TER Rue de Châteaudun
		[city] => Boulogne-Billancourt
		[housenumber] => 1 TER
		[score] => 0.99999997442737
		[type] => housenumber*/
		//$ret=$r['features'][0]['properties']['label'];
		//$ret=$r['features'][0]['properties']['city'];

//vote
	/*
	static function add_x(){
		return self::edit($com,$p,$v);
		$ret=self::textarea();
		$ret.=aj('pllscnt,,x|vote,pollSave||text',lang('add'),'btsav');
		return $ret;}*/

//ajax.js
/*function toggle_close(did){var id=getbyid(did).dataset.bid;
	var btn=getbyid(id); closediv(did); btn.rel=''; active(btn,0);}*/

?>