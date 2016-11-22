<?php

class Conn{
	static $one=0;

	static function mklist($d){
		$r=explode("\n",$d);
		foreach($r as $v)$ret[]=tag('li','',$v);
		return tag('ul','',implode('',$ret));
	}
	
	static function url($d,$c='',$o=''){
		$r=explode('*',$d); $u=$r[0]; $t=isset($r[1])?$r[1]:domain($r[0]);
		return href($u,$t,$c,$o);}
	
	static function reader($d,$b){
		list($p,$o,$c)=readconn($d);$atb=array('class'=>$o);//echo $p.'*'.$o.':'.$c.br();;
		$b='b,i,u,h1,h2,h3,h4,span,div,small,big,';
		if(strpos($b,$c.',')!==false)return tag($c,$atb,$p);
		switch($c){
			case('br'):return br(); break;
			case('h'):return tag('h3',$atb,$p); break;
			case('e'):return tag('sup',$atb,$p); break;
			case('s'):return tag('small',$atb,$p); break;
			case('k'):return tag('strike',$atb,$p); break;
			case('q'):return tag('blockquote',$atb,$p); break;
			case('url'):return href($p,$o); break;
			//case('url'):return self::url($d,'btn'); break;
			case('tag'):return tag($c,$o,$p); break;
			case('pic'):return pico($p,$o); break;
			//case('picto'):return picto($p,$o); break;
			case('list'):return self::mklist($p); break;
			case('art'):return href('/art/'.$p,article::tit(['id'=>$p]),'btlk'); break;
			//case('form'):return Form::com($p); break;
			case('apj'):$js='ajaxCall("div,cn'.$c.',,1|'.$p.','.$o.'","headers=1");';
				return div(Head::jsCode($js),'','cn'.$c); break;
			case('app'):return App::open($p,['param'=>$o,'headers'=>1]); break;
			case('appbt'):return ajax('popup',$c.','.$o,'param='.$p,'',langp('open').' '.$c.':'.$p,'btn'); break;
			case('no'):return '['.$p.']'; break;
			//default:return '['.$d.']'; break;
		}
		if(is_img($d))return img($d,'','',$o);
		if($p=='http')return self::url($d);
		if($c){//app as connector
			if($o==1)return ajax('popup',$c.','.$o,'param='.$p,'',langp('open').' '.$c.':'.$p,'btn');
			else return App::open($c,['appMethod'=>$o,'param'=>$p,'headers'=>1]);}
		return '['.$d.']';
	}
	
	static function read($d,$app,$mth,$p=''){
	$st='['; $nd=']'; $deb=''; $mid=''; $end='';
	$in=strpos($d,$st);
	if($in!==false){
		$deb=substr($d,0,$in);
		$out=strpos(substr($d,$in+1),$nd);
		if($out!==false){
			$nb_in=substr_count(substr($d,$in+1,$out),$st);
			if($nb_in>=1){
				for($i=1;$i<=$nb_in;$i++){$out_tmp=$in+1+$out+1;
					$out+=strpos(substr($d,$out_tmp),$nd)+1;
					$nb_in=substr_count(substr($d,$in+1,$out),$st);}
				$mid=substr($d,$in+1,$out);
				$mid=self::read($mid,$app,$mth,$p);}
			else $mid=substr($d,$in+1,$out);
			$mid=$app::$mth($mid,$p);
			$end=substr($d,$in+1+$out+1);
			$end=self::read($end,$app,$mth,$p);}
		else $end=substr($d,$in+1);}
	else $end=$d;
	if($p=='bchain'){
		if(is_array($deb) && is_array($mid))
			$ret=array_merge_recursive($deb,$mid); else $ret=$mid;
		if(is_array($end))$ret=array_merge_recursive($ret,$end);
		return $ret;}
	return $deb.$mid.$end;}
	
	static function load($p){
		$d=val($p,'msg',val($p,'params')); $opt=val($p,'opt'); $ptag=val($p,'ptag'); 
		$app=val($p,'app','Conn'); $mth=val($p,'mth','reader'); self::$one=0;
		$ret=self::read($d,$app,$mth,$opt);
		if($ptag)$ret=nl2br(ptag($ret));
		else $ret=mb_ereg_replace("[\n]{2,}",br(),$ret);
		//else $ret=nl2br($ret);
		return $ret;
	}
}
?>