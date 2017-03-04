<?php
class Build{
#popup
static function popup($d,$prm){
	$pw=val($prm,'pagewidth'); $w=val($prm,'popwidth');
	$style='min-width:320px;';
	$style.=' max-width:'.($pw<640?$pw:($w?$w:$pw-100)).'px;';
	$cl=picto('close',20); $min=picto('less',20); $rez=picto('ktop',20);
	//$cl=ico('close',16); $min=ico('window-minimize',16); $rez=ico('window-restore',16);
	$ret=tag('a',['class'=>'imbtn','onclick'=>'Close(\'popup\');'],$cl);
	$ret.=tag('a',['class'=>'imbtn','onclick'=>'Reduce(\'popup\');'],$min);
	$ret.=tag('a',['class'=>'imbtn','onclick'=>'Repos();'],$rez);		
	$app=val($prm,'appName'); $mth=val($prm,'appMethod');
	$lk=href('/app/'.$app,ico('link'),'',1).' ';
	$title=$lk.$app;//.($mth?'::'.$mth:'');
	$title=val($prm,'title',$title);
	if($app && method_exists($app,'admin') && !$mth)
		$title.=Menu::call(['app'=>$app,'method'=>'admin']);
	$ret.=tag('span',['class'=>'imbtn'],$title);
	$header=tag('div',['id'=>'popa','class'=>'popa','onmouseup'=>'stop_drag(event); noslct(1);','onmousedown'=>'noslct(0);'],$ret);
	$d=tag('div',['id'=>'popu','class'=>'popu'],$d);
	return tag('div',['class'=>'popup','style'=>$style],$header.$d);}

static function pagup($d){
	$close='';//span(btj(ico('close'),'Close(\'popup\');','btn'),'left');
	$d=tag('div',['id'=>'popu','class'=>'pagu'],div($close.$d,'pgu'));
	return tag('div',['class'=>'pagup'],$d);}

static function imgup($d){
	$ret=tag('div',['id'=>'popu','class'=>'imgu'],div($d,'imu'));
	//$ret=tag('a',['onclick'=>'Close(\'popup\');'],$ret);
	return tag('div',['class'=>'pagup'],$ret);}

#bubble
static function bubble($d){
	$d=tag('div',['id'=>'popu','class'=>'bubu'],$d);
	return tag('div',['class'=>'bubble','style'=>'max-width:320px'],$d);}

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
	//$v=str_replace("\t",'',$v);
	$v=highlight_string($v,true);
	if($o)$v=str_replace(['FF8000','007700','0000BB','DD0000','0000BB'],['FF8000','00ee00','afafff','eeeeee','ffbf00'],$v);
	$v=str_replace(['&lt;?php&nbsp;','&lt;?php','?&gt;'],'',$v);
	if(substr($v,0,6)=='<br />')$v=substr($v,6);
	if(substr($v,0,4)=='<br>')$v=substr($v,4);
	return trim($v);}

//table
static function table($array,$csa='',$csb='',$keys=''){$i=0; $tr='';
	if(is_array($array))
	foreach($array as $k=>$v){$td=''; $i++;
		$cs=$i==1?$csa:$csb;
		$alterenateCss=$i%2?'r2':'r1';
		if($keys)$td.=tag('td',['class'=>$cs],$k);
		if(is_array($v))foreach($v as $ka=>$va)
			$td.=tag('td',['class'=>$cs],$va);
		else $td.=tag('td',['class'=>$cs],$v);
		if($td)$tr.=tag('tr',['id'=>$k,'valign'=>'top','class'=>$alterenateCss],$td);}
	return tag('table','',$tr);}

//html table
static function divTable($array,$csa='',$csb=''){$ret=''; $i=0;
	if(is_array($array))foreach($array as $k=>$v){$td=''; $i++;
		$cs=$i==1?$csa:$csb;
		$alterenateCss=$i%2?'r2':'r1';
		if(is_array($v))foreach($v as $ka=>$va)
			$td.=tag('span',['class'=>'cell2 '.$cs],$va);
		$ret.=tag('span',['id'=>$k,'class'=>'row '.$alterenateCss],$td);}
	return $ret;}
}
?>