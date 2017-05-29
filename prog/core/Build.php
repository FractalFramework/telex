<?php
class Build{
#popup
static function popup($d,$p){
	$pw=val($p,'pagewidth'); $w=val($p,'popwidth');
	$style='min-width:320px;';
	$style.=' max-width:'.($pw<640?$pw:($w?$w:$pw-100)).'px;';
	$cl=picto('close',20); $min=picto('less',20); $rez=picto('ktop',20);
	//$cl=ico('close',16); $min=ico('window-minimize',16); $rez=ico('window-restore',16);
	$ret=tag('a',['class'=>'imbtn','onclick'=>'Close(\'popup\');'],$cl);
	$ret.=tag('a',['class'=>'imbtn','onclick'=>'Reduce(\'popup\');'],$min);
	$ret.=tag('a',['class'=>'imbtn','onclick'=>'Repos();'],$rez);		
	$app=val($p,'appName'); $mth=val($p,'appMethod');
	//titles
	if(method_exists($app,'titles'))$title=$app::titles($p);
	else $title=href('/app/'.$app,ico('link'),'',1).' '.$app;
	if($app && method_exists($app,'admin') && !$mth)
		$title.=Menu::call(['app'=>$app,'method'=>'admin']);
	$ret.=tag('span',['class'=>'imbtn'],$title);
	$header=tag('div',['id'=>'popa','class'=>'popa','onmouseup'=>'stop_drag(event); noslct(1);','onmousedown'=>'noslct(0);'],$ret);
	$d=tag('div',['id'=>'popu','class'=>'popu'],$d);
	return tag('div',['class'=>'popup','style'=>$style],$header.$d);}

static function pagup($d,$p){if(!$d)return;
	if($w=val($p,'popwidth'))$d=div($d,'','','max-width:'.$w.'px');
	//$close=span(btj(ico('close'),'Close(\'popup\');','btn'),'left');
	$d=tag('div',['id'=>'popu','class'=>'pagu'],div($d,'pgu'));
	return tag('div',['class'=>'pagup'],$d);}

static function imgup($d){
	$ret=tag('div',['id'=>'popu','class'=>'imgu'],div($d,'imu'));
	//$ret=tag('a',['onclick'=>'Close(\'popup\');'],$ret);
	return tag('div',['class'=>'pagup'],$ret);}

#bubble
static function bubble($d){
	$d=tag('div',['id'=>'popu','class'=>'bubu'],$d);
	return tag('div',['class'=>'bubble'],$d);}//,'style'=>'max-width:320px'

static function menu($d){
	$d=tag('div',['id'=>'popu','class'=>'bubu'],$d);
	return tag('div',['class'=>'bubble','style'=>''],$d);}

#scroll
static function scroll($r,$n,$h=''){$max=count($r); $ret=implode('',$r);
	$s='overflow-y:scroll; max-height:'.($h?$h.'px':400).';';
	if($max>$n)return tag('div',['id'=>'scroll','style'=>$s],$ret); 
	else return $ret;}

static function code($v,$o=''){
	$v=str_replace(['<?php','?>'],'',$v);
	$v='<?php '.trim($v).' ?>';
	$v=highlight_string($v,true);
	if($o)$v=str_replace(['FF8000','007700','0000BB','DD0000','0000BB'],['FF8000','00ee00','afafff','eeeeee','ffbf00'],$v);
	$v=str_replace(['&lt;?php&nbsp;','&lt;?php','?&gt;'],'',$v);
	if(substr($v,0,6)=='<br />')$v=substr($v,6);
	if(substr($v,0,4)=='<br>')$v=substr($v,4);
	return trim($v);}

//table
static function table($r,$css='',$keys='',$head=''){$i=0; $tr='';
	if(is_array($r))foreach($r as $k=>$v){$td=''; $i++;
		$tag=$head && $i==1?'th':'td';
		if($keys)$td.=tag($tag,'',$k?$k:'');
		if(is_array($v))foreach($v as $ka=>$va)
			$td.=tag($tag,'',$va);
		else $td.=tag($tag,'',$v);
		if($td)$tr.=tag('tr',['id'=>$k],$td);}
	$ret=tag('table',['class'=>$css],$tr);
	return div(tag('tbody','',$ret),'','','overflow:auto;');}

//html table
static function divTable($r,$csa='',$csb=''){$ret=''; $i=0;
	if(is_array($r))foreach($r as $k=>$v){$td=''; $i++;
		$cs=$i==1?$csa:$csb;
		$alterenateCss=$i%2?'r2':'r1';
		if(is_array($v))foreach($v as $ka=>$va)
			$td.=tag('span',['class'=>'cell2 '.$cs],$va);
		$ret.=tag('span',['id'=>$k,'class'=>'row '.$alterenateCss],$td);}
	return $ret;}

static function toggle($p){$v=$p['v']; $rid=randid('itg'); 
	if($v==1){$ic='on'; $t='yes';}else{$ic='off'; $t='no';}
	$j=$rid.'|Build,toggle|id='.$p['id'].',v='.($v==1?0:1); $t=$v==1?'yes':'no';
	return span(aj($j,ico('toggle-'.$ic,22).lang($t)).hidden($p['id'],$v),'',$rid);}

static function leftime($end){$time=$end-ses('time');
	if($time>86400)$ret=($n=floor($time/86400)).' '.langs('day',$n);
	elseif($time>3600)$ret=($n=floor($time/3600)).' '.langs('hour',$n);
	elseif($time>60)$ret=($n=floor($time/60)).' '.langs('minute',$n);
	else $ret=$time.' '.langs('second',$time);
	return span($ret,'small');}

static function wysiwyg($id){$ret='';
	$r=['bold'=>'bold','italic'=>'italic','underline'=>'underline','insertUnorderedList'=>'list-ul','insertOrderedList'=>'list-ol','Indent'=>'indent','Outdent'=>'outdent','createLink'=>'link'];
	//,'JustifyLeft'=>'align-left','JustifyCenter'=>'align-center','inserthorizontalrule'=>'minus'
	foreach($r as $k=>$v)$ret.=tag('button',['onclick'=>atj('format',$k)],ico($v,14));
return div($ret,'connbt');}//

static function thumb($f,$dim){$dr='img/';
	$fb='medium/'.$f; $med=is_file($dr.$fb);
	if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
	elseif($dim=='medium')$im=$med?$fb:'full/'.$f; else $im='full/'.$f;
	if(is_file($dr.$im) && filesize($dr.$im))return $dr.$im;}

static function mini($d){
	$fa='img/mini/'.$d; $fb='img/full/'.$d;
	if(is_file($fb))return imgup($fb,img('/'.$fa));}

}
?>