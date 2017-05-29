<?php

class Conn{
static $one=0;

static function mklist($d,$o=''){
	$r=explode("\n",$d); $b=$o?'ol':'ul';
	foreach($r as $v)if(substr($v,0,1)=='<')$ret[]=$v; else $ret[]=tag('li','',$v);
	return tag($b,'',implode('',$ret));}
	
static function mktable($p,$o=''){
	if(strpos($p,'¬')===false && strpos($p,'|') && strpos($p,"\n"))$p=str_replace("\n",'¬',$p);
	$p=str_replace(array('|¬',"¬\n",' ¬'),'¬',$p);
	if(substr(trim($p),-1)=='¬')$p=substr(trim($p),0,-1);
	$tr=explode('¬',$p);
	foreach($tr as $k=>$row)$ret[]=explode('|',$row);
	return Build::table($ret,'','',$o);}
	
static function url($u,$t='',$c='',$o=''){//$t=$t?$t:domain($u);
	//if(substr($u,0,4)=='http'){$t=ico('external-link',12).$t; $o=1;}
	return href($u,$t,$c,$o);}

static function img($p,$o){
	if($o)$oa=explode('-',$o); $w=isset($oa[0])?$oa[0]:''; $h=isset($oa[1])?$oa[1]:'';
	if(strpos($p,'/')===false)return img('/'.Build::thumb($p,'full'),$w,$h);
	elseif(substr($p,0,1)=='/')return img($p,$w,$h);
	else return img($p,$w,$h);}

static function noconn($d,$b){
	list($p,$o,$c)=readconn($d); $atb['class']=$o;
	$r=['a','b','i','u','e','n','h1','h2','h3','h4','span','div','small','big','url'];
	if(in_array($c,$r))return $p;
	switch($c){
		case('url'):return $p; break;
		case('img'):return substr($p,0,4)=='http'?$p:host().'/img/full/'.$p; break;
		case('web'):return Sql::read('tit','tlex_web','v','where url="'.$p.'"'); break;}
	if($c && method_exists($c,'tit')){$q=new $c; $t=$q::tit(['id'=>$p]);
		return $t.' : '.host().'/'.$c.'/'.$p;}
	return $o?$o:$p;}

static function minconn($d,$b){
	list($p,$o,$c)=readconn($d); $atb['class']=$o; //echo $p.'*'.$o.':'.$c.br();
	$r=['b','i','u','e','n','h1','h2','h3','h4','small','big','span','div'];//,'code'
	if(in_array($c,$r))return tag($c,$atb,$p);//.($o?'*'.$o:'')
	switch($c){
		case('-'):return hr(); break;
		case('h'):return tag('h3',$atb,$p); break;
		case('k'):return tag('strike',$atb,$p); break;
		case('q'):return tag('blockquote',$atb,$p); break;
		case('a'):return href($p,$o,'btxt'); break;
		case('url'):return self::url($p,$o,'btxt'); break;
		case('img'):return self::img($p,$o); break;
		case('list'):return self::mklist($p); break;
		case('numlist'):return self::mklist($p,1); break;
		case('table'):return self::mktable($p,$o); break;}
	return '['.$d.']';}
	
static function reader($d,$b){
	list($p,$o,$c)=readconn($d); $atb['class']=$o; //echo $p.'*'.$o.':'.$c.br();
	$r=['b','i','u','h1','h2','h3','h4','sub','big','span','div'];//,'code'
	if(in_array($c,$r))return tag($c,$atb,$p);//.($o?'*'.$o:'')
	switch($c){
		case('br'):return br(); break;
		case('-'):return hr(); break;
		case('h'):return tag('h3',$atb,$p); break;
		case('k'):return tag('strike',$atb,$p); break;
		case('q'):return tag('blockquote',$atb,$p); break;
		case('s'):return tag('small',$atb,$p); break;
		case('e'):return tag('sup',$atb,$p); break;
		case('n'):return tag('sub',$atb,$p); break;
		case('a'):return href($p,$o,'btxt'); break;
		case('url'):return self::url($p,$o,'btxt'); break;
		case('img'):return self::img($p,$o); break;
		case('list'):return self::mklist($p); break;
		case('numlist'):return self::mklist($p,1); break;
		case('table'):return self::mktable($p,$o); break;
		case('web'):return tlex::playweb($p); break;
		case('tag'):return tag($c,$o,$p); break;
		case('ico'):return ico($p,$o); break;
		case('pic'):return pic($p,$o); break;
		case('picto'):return picto($p,$o); break;
		case('lang'):return lang($p,$o); break;
		case('help'):return help($p,$o); break;
		//case('css'):return span($p,$o); break;
		case('code'):return tag('pre','',tag('code','',$p.($o?'*'.$o:''))); break;
		case('artit'):return href('/art/'.$p,art::tit(['id'=>$p]),'btlk'); break;
		case('art'):return App::open($c,(['id'=>$p]),''); break;
		case('form'):return forms::conn(['fcom'=>$p]); break;
		case('apj'):$js='ajaxCall("div,cn'.$c.',,1|'.$p.','.$o.'","headers=1");';
			return div(Head::jsCode($js),'','cn'.$c); break;
		case('app'):return App::open($p,['param'=>$o,'headers'=>1]); break;
		case('popup'):return popup($p,$o,'btxt'); break;
		case('pagup'):return pagup($p,$o,'btxt'); break;
		case('imgup'):return imgup($p,$o); break;
		case('aj'):return aj($p,$o,'btxt'); break;
		case('on'):return '['.$p.($o?'*'.$o:'').']'; break;
		case('no'):return '['.$p.']'; break;}
	if(is_img($d))return img($d,'','',$o);
	if($p=='http')return self::url($d,'','btxt');
	if($c && method_exists($c,'content')){//app as connector
		if($o==1)return aj('popup|'.$c.','.$o.'|param='.$p,langp('open').' '.$c.':'.$p,'btn');
		else return App::open($c,['appMethod'=>$o,'param'=>$p,'headers'=>1]);}
	return '['.$d.']';}
	
static function read($d,$app,$mth,$p=''){
	$st='['; $nd=']'; $deb=''; $mid=''; $end='';
	$in=strpos($d,$st);
	if($in!==false){
		$deb=substr($d,0,$in);
		$out=strpos(substr($d,$in+1),$nd);
		if($out!==false){
			$nb=substr_count(substr($d,$in+1,$out),$st);
			if($nb>=1){
				for($i=1;$i<=$nb;$i++){$out_tmp=$in+1+$out+1;
					$out+=strpos(substr($d,$out_tmp),$nd)+1;
					$nb=substr_count(substr($d,$in+1,$out),$st);}
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
	$d=str_replace("<br />\n","\n",$d);
	$d=str_replace('<br />','',$d);
	$app=val($p,'app','Conn'); $mth=val($p,'mth','reader'); self::$one=0;
	$ret=self::read($d,$app,$mth,$opt);
	if($ptag==1)$ret=ptag($ret);
	elseif($ptag!='no')$ret=nl2br($ret);
	return $ret;}
	
static function call($p,$o=''){return self::load(['msg'=>$p,'ptag'=>$o]);}
}
?>