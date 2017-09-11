<?php
//trash

//appx

/*static function form2($p){
	$cb=self::$cb; $ret=''; $db=self::$db2; $id=$p['id']; $r['bid']=$id;
	$cols=Sql::columns($db,2); $cls=Sql::columns($db,3); $uid=val($p,'uid');
	foreach($cols as $k=>$v){$val=val($p,$k); $label='';
		if($k=='bid')$bt=hidden($k,$id);
		elseif($k=='uid')$bt=hidden($k,ses('uid'));
		elseif($v=='var')$bt=input($k,$val,63,'','',255);
		elseif($v=='text')$bt=textarea($k,$val,60,12,'');
		elseif($v=='date')$bt=inp($k,$val?$val:date('Y-m-d',time()),8,'');
		elseif($v=='int')$bt=inp($k,$val,8,'');
		if($k!='bid' && $k!='uid')$label=label($k,lang($k),'');
		$ret.=div(div($label,'row').div($bt,'cell'),'row');}
	$bt=aj(self::$cb.'|'.self::$a.',save2|id='.$id.'|'.$cls,langp('save'),'btsav');
	$ret.=div(div('','row').div($bt,'cell'),'row');
	return $ret;}*/

//tlex///pane
/*if($id=$_POST['repost']){
	$by=bubble('tlex,profile|usr='.$usr,'@'.$usr,'',1);
	$ret=div($by.' '.lang('has_repost',1),'grey');
	$ret.=$msg;
	//$ret.=self::playquote($id);
	$r=self::api(['id'=>$id]);
	return $ret.self::pane($r[0]);}*/

//tlxcall
/*static function menuapps1($p){$ret=''; $rid=val($p,'rid'); $dr=val($p,'dir');
$dir='/apps/tlex'.($dr?'/'.$dr:'');
$r=Sql::read('com','desktop','rv','where dir="'.$dir.'" and auth<="'.ses('auth').'"'); //p($r);
$prm['onclick']='closebub(event);';
foreach($r as $k=>$v){$bt=pic($v,28).span(hlpxt($v));
	if(method_exists($v,'com'))
		$ret.=aj('tlxapps,,,1|'.$v.',com|headers=1,rid='.$rid,$bt,'cicon',$prm);}
return $ret;}

static function menuapps0($p){$ret=''; $rid=val($p,'rid');
$r=Sql::read('distinct(substring(dir,12))','desktop','rv','where dir like "/apps/tlex/%" and auth<="'.ses('auth').'"');
foreach($r as $k=>$v)
	$ret.=aj('applist|tlxcall,menuapps1|rid='.$rid.',dir='.$v,langp($v));
return div($ret,'cell list','','').div('','cell','applist','');}*/

//tlex
//publishbt
/*static function saveapp($p){$txt=val($p,$p['ids']);
$txt=self::build_conn($txt,1); $ib=val($p,'ibs',0);
if($lbl=post('lbl'))$_POST['lbl']='';
if($lbl && !is_numeric($lbl))$lbl=Sql::read('id','labels','v','where ref="'.$lbl.'"');
$id=Sql::insert(self::$db,array(ses('uid'),$txt,(int)$lbl,$ib,0));
if(isset($_POST['ntf']))self::saventf($id,1,'ntf');
if(isset($_POST['ntf-r']))self::saventf($id,2,'ntf-r');
return self::read($p);}*/

//lib
/*function langs($d){$r=explode(',',$d); foreach($r as $v)$ret[]=Lang::get($v);
	return ucfirst(implode(' ',$ret));}*/

//bank
/*static function coin($typ,$n){$ret='';
	for($i=10;$i>0;$i--){
		$c=$i<=$n?$typ:'';
		if($i<=$n)$bt=$i; else $bt='';
		$ret.=div($bt,'coin '.$c);}
	$ret.=div($typ.': '.$n,'coin');
	return div($ret,'coin_block');}*/

/*static function coin0($typ,$n){$w=80; $h=180; $hb=($h-20)/10;
	if($typ=='red')$clr='red'; elseif($typ=='blue')$clr='blue'; 
	elseif($typ=='green')$clr='green'; else $clr='grey';
	$ret='['.$clr.',white,1:attr]'; //echo $typ.$clr.'-';
	for($i=9;$i>=0;$i--){
		if($i<10-$n)$ret.='[white,0,1:attr]';
		$ret.='[0,'.($i*$hb).','.$w.','.$hb.':rect]';}
	$ret.='[black,black,0:attr]'; //echo $typ.$clr.'-';
	$ret.='[0,'.($h-4).',,text-align:center;*'.$typ.':'.$n.':text]';
	return Svg::call(['code'=>$ret,'size'=>$w.'/'.$h]);}*/

//utils
/*function isMobile(){if(navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i) || navigator.userAgent.match(/Opera Mini/i) || navigator.userAgent.match(/IEMobile/i))return true;}

function removeMobileOnclick(){if(isMobile())
document.querySelector('a').onclick='';}

window.addEventListener('load',removeMobileOnclick);*/

//lib
/*function dropdown($call,$t,$c='',$r=''){$id=randid('bb');//$r['id']=$id;
	return span(aj('bubble,'.$id.',1|'.$call,$t,$c,$r),'',$id);}*/
/*function bub($mode,$call,$t,$o='',$r=''){$id=randid('bb'); $r['id']=$id;
	return span(aj($mode.','.$id.','.$o.'|'.$call,$t,'',$r));}*/

//poll
/*static function userdate($ts,$name){
	$date=span(date('d/m/Y',$ts),'small');
	return $date.' '.small(lang('by').' '.$name).' ';}*/

/*static function daytotime($d=1){return $d*86400;}
static function outdate($date,$day){$timelimit=self::daytotime($day);
	if(ses('time')-$date>$timelimit)return 1; else return 0;}*/

/*private static function security($table,$id){
	$uid=Sql::read('uid',$table,'v',$id);
	if($uid==ses('uid'))return 1;}*/

/*static function textarea($v=''){
	return textarea('text',$v,70,4,lang('description'),'',216).br();}*/

#create
/*static function update($p){
	if($p['id'])Sql::update(self::$db,'txt',$p['text'],$p['id']);
	return self::edit($p);}*/

/*static function mdfcnt($p){$id=val($p,'id');
	$txt=Sql::read('txt',self::$db,'v',$id);
	$ret=self::textarea($txt);
	$ret.=aj(self::$cb.'|poll,modif|id='.$id.'|text',lang('save'),'btsav');
	return $ret;}*/

/*static function del0($p){$id=$p['id'];
	if(!self::security(self::$db,$id))return;
	if(!val($p,'ok'))return aj(self::$cb.'|poll,del|ok=1,rid='.val($p,'rid').',id='.$id,lang('confirm deleting'),'btdel');
	if($id){Sql::delete(self::$db,$id);
		Sql::delete('poll_valid',$id,'bid');}
	return self::stream($p);}*/

/*static function save($p){
	for($i=1;$i<10;$i++)if($v=val($p,'answ'.$i))$answ[]=$v;
	if(!isset($answ))return help('poll empty'); $answers=implode('|',$answ);
	$r=[ses('uid'),$p['text'],$answers,$p['nbdays']];
	if($p['text'])$p['id']=Sql::insert(self::$db,$r);
	return self::edit($p);}*/

#add
/*static function create($p){
	$nb=val($p,'nb',2); if($nb>10)$nb=10;
	$rid=val($p,'rid'); $nbd=val($p,'nbdays',1);
	$inp[]='text'; $inp[]='nbdays'; 
	for($i=1;$i<=$nb;$i++)$inp[]='answ'.$i; $inps=implode(',',$inp);
	$ret=aj(self::$cb.'|poll,save|rid='.$rid.'|'.$inps,lang('save'),'btsav');
	$ret.=div(lang('ask a question').' :','stit');
	$ret.=self::textarea(val($p,'text'));
	for($i=1;$i<=$nb;$i++)$ret.=div(input('answ'.$i,val($p,'answ'.$i),'',lang('choice').' '.$i));
	$ret.=aj('newpoll|poll,create|rid='.$rid.',nb='.($nb+1).'|'.$inps,langp('add choice'),'btn').br();
	$ret.=label('nbdays',lang('nb days',1)).' '.bar('nbdays',$nbd,1,1,7);
	return div($ret,'','newpoll');}*/

//vote
/*private static function security($table,$id){
	$uid=Sql::read('uid',$table,'v',$id);
	if($uid==ses('uid'))return 1;}*/

/*static function update($p){
	$r=['txt'=>$p['text'],'day'=>$p['day']];
	if($p['id'])Sql::updates(self::$db,$r,$p['id']);
	return nl2br($p['text']);}*/

/*static function del0($p){$id=val($p,'id'); $rid=val($p,'rid'); $ok=val($p,'ok');
	if(!self::security(self::$db,$id))return;
	if(!$ok)return aj('blcbk|vote,del|rid='.$rid.',id='.$id.',ok=1',lang('confirm deleting'),'btdel');
	elseif($id){Sql::delete(self::$db,$id);
		Sql::delete('vote_note',$id,'bid');}
	return self::stream($p);}*/

/*static function save0($p){
	for($i=0;$i<20;$i++)if($v=val($p,'answ'.$i))$answ[]=$v;
	if(!isset($answ))return help('vote empty');
	$r=[ses('uid'),$p['text'],implode('|',$answ),$p['day']];
	if($p['text'])$p['id']=Sql::insert(self::$db,$r);
	if(isset($p['id']))return self::edit($p);
	else return help('vote empty');}*/

/*static function formcreate($txt,$fin){
	if(!$fin)$fin=date('Y-m-d',ses('time')+(7*86400));
	$ret=input('day',$fin,'',lang('date of end')).br();
	$ret.=textarea('text',$txt,70,4,lang('description'),'','',140).br();
	return $ret;}

static function create0($p){$nb=val($p,'nb',2); $rid=val($p,'rid');
	$inp=['text','day']; for($i=1;$i<=$nb;$i++)$inp[]='answ'.$i; $inps=implode(',',$inp);
	$ret=aj('blcbk|vote,save|rid='.$rid.'|'.$inps,lang('save'),'btsav').br();
	$ret.=self::formcreate(val($p,'text'),val($p,'day'));
	for($i=1;$i<=$nb;$i++)$ret.=div(input('answ'.$i,val($p,'answ'.$i),'',lang('choice').' '.$i));
	if($nb<20)$ret.=aj('newvote|vote,create|rid='.$rid.',nb='.($nb+1).'|'.$inps,langp('add choice'),'btn').br();
	if(val($p,'nb'))return $ret;
	return div($ret,'','newvote');}*/

//appx
/*static function captures($p,$o=''){
	$rc=self::$cols; if($o)$ret=[ses('uid')]; $answ=[];
	if(in_array('answ',$rc)){//p($p);
		for($i=1;$i<20;$i++)if($v=val($p,'answ'.$i))$answ[]=$v; //p($answ);
		$p['answ']=implode('|',$answ);}
	foreach($rc as $v)$ret[$v]=val($p,$v); //pr($ret);
	return $ret;}*/
	//$r=self::captures($p,1);

/*static function answ0($p){$nm=$p['nm']; $val=protect($p['val'],1); $ret='';
	$r=explode('|',$val); if(val($p,'add'))$r[]=''; $n=count($r);
	foreach($r as $k=>$v){$ret.=div(input($nm.$k,$v));}
	$ret.=aj($nm.'vals|appx,answ|add='.$n.',nm='.$nm.',val='.protect($val),langp('add'),'btn');
	$ret.=hidden($nm,$val);
	return div($ret,'',$nm.'vals');}*/

/*static function ransw(){$r=self::$cols;
	foreach($r as $v)if($v=='answ')$re[]=post('answ'); else $re[]=$v;
	return implode(',',$re);}*/

///edit
//$cls=self::ransw();
	//$edition.=aj($cb.'|'.$a.',modif|id='.$id.',rid='.$rid.'|'.$cls,langp('save'),'btsav');//

/*static function preview($p){
	$ret=aj(self::$cb.'|'.self::$a.',edit|id='.$p['id'].',rid='.$p['rid'],pic('refresh'),'btn');
	$ret.=self::play($p);
	return div($ret,'',self::$cb.$p['id']);}*/

/*static function appmenu($p){$id=val($p,'id'); $a=self::$a; $cb=$a=self::$cb;
	$ret=aj($cb.'|'.$a.',edit|id='.$p['id'].',rid='.$p['rid'],pic('back'),'btn');
	$ret.=aj($cb.'|'.$a.',call|id='.$p['id'].',rid='.$p['rid'],pic('refresh'),'btn');
	$ret.=http('/'.$a.'/'.$id,ico('url'));
	return div($ret,'');}*/
	//if(val($p,'prw'))$ret=self::appmenu($p);

//tlex
/*static function display_app($c,$p,$o){if(method_exists($c,'call')){Conn::$one=1;
	self::$objects[$c][]=[$p,$o];
	if(Conn::$one!=1)$ret=App::open($c,['appMethod'=>'call','conn'=>'no','id'=>$p]);
	else{$ob=new $c; $t=$o?$o:$c::tit(['id'=>$p]); $bt=span(hlpic($c),'app').' '.span($t,'btxt');
		$ret=pagup($c.',call|headers=1,popwidth=440,id='.$p,$bt,'object');}
return div($ret);}}*/

//profile
/*static function init_clr($usr){
	if($clr=ses('clr'.$usr))return $clr;
	$clr=Sql::read('clr',self::$db,'v','where pusr="'.$usr.'"');
	return ses('clr',$clr?$clr:self::$default_clr);}*/

//appx
/*static function stream($p){$rid=val($p,'rid'); $ret=''; $w='';
	$a=self::$a; $db=self::$db; $cb=self::$cb; $cols=self::$cols; 
	$t=val($p,'t',$cols[0]); $uid=ses('uid');
	$dsp=ses($a.'dsp',val($p,'display'));
	$r=Sql::read('id,uid,'.$t.',dateup',$db,'rr','where uid="'.$uid.'" order by id desc');
	if($r)foreach($r as $k=>$v){
		$tit=$v[$t]?$v[$t]:'#'.$v['id']; $com='edit'; $ic='file-o';
		$btn=ico($ic).$tit.' '.span($v['date'],'date');
		$c=$dsp==1?'bicon':'licon';
		$ret.=aj($cb.'|'.$a.','.$com.'|id='.$v['id'].',rid='.$rid,$btn,$c);}
	if(!$ret)$ret=help('no element','txt');
	return div($ret,'');}*/

//tabler
/*static function trans($d,$mode='conn'){
	//$d=html_entity_decode($d);
	//$d=str_replace(array('&amp;',"&lt;","&gt;"),array('&','<','>'),$d);
	$d=str_replace(array('<table>','</table>','<tbody>','</tbody>','</tr>','</td>','</th>'),'',$d);
	if(strpos($d,'<th'))$head=1; else $head=0;
	$d=str_replace('<th','<td',$d);
	//$d=delbr($d,"\n");
	$ra=explode('<tr',$d);
	if($ra)foreach($ra as $k=>$v){
		$v=substr($v,strpos($v,'>')+1);
		$rb=explode('<td',$v);
		foreach($rb as $kb=>$vb){
			$vb=substr($vb,strpos($vb,'>')+1);
			$rc[$k][$kb]=trim(html_entity_decode(strip_tags($vb)));}}
	if(!isset($rc))return;
	if($mode=='data')return $rc;
	if(isset($rc)){
		if($mode=='conn')return arrayToString($rc,'¬','|');
		elseif($mode=='json')return json_encode($rc,JSON_PRETTY_PRINT);
		elseif($mode=='sql')return Sql::mysql_array2($rc);}}*/

//utils.js
//art
/*function wygedtoff(id){getbyid("edt"+id).style.display="none";}
function wygedt(e,id){if(getbyid("txt"+id).className=="editon"){
	getrange("txt"+id);//
	var ob=getbyid("edt"+id); ob.style.display=""; var m=mouse(e);
	var w=ob.offsetWidth/2; if(w<10)w=10; //var h=ob.offsetHeight-50;
	ob.style.left=(m.x-w+20)+"px"; ob.style.top=(m.y-40)+"px";}}*/

/*function editxt(div,id){var ob=getbyid(div+id);
	if(ob.className!="editon"){
		//addEvent(ob,"click",function(){editxt(div,id)});
		//addEvent(ob,"blur",function(){savtxt(div,id)});
		if(div=="txt"){
			ajaxCall("div,txt"+id+"|art,playconn","id="+id,"");}
			//addEvent(ob,"dblclick",function(event){wygedt(event,id)});
			//addEvent(ob,"click",function(){wygedtoff(id)});
		ob.contentEditable="true"; ob.designMode="on"; void 0; //ob.focus();
		ob.className="editon";}}*/

//ballot
/*static function pane_results0($rb,$id){$ret='';
	$r=Sql::read('choice,val','ballot_valid','kkc','where idballot="'.$id.'"');
	//collect scores
	foreach($r as $k=>$v){$stot=0; $tot=array_sum($v);//nb votes
		for($i=1;$i<=5;$i++){
			$ratio=isset($v[$i])?round($v[$i]/$tot,2):0; $stot+=$ratio;//% of vote $i
			$rd[$k][$i]=$ratio; $re[$i][$k]=$stot;}}
	//define order recursively
	$rok='';
	if(isset($re))for($i=1;$i<=5;$i++){arsort($re[$i]);//scores by vote
		if($rok){foreach($rok as $v)unset($re[$i][$v]);}
		if($re[$i]){$rf='';
			//$max=max($re[$i]); $mxk=in_array_k($re[$i],$max);
			$max=current($re[$i]); $mxk=key($re[$i]);//best
			//other soluces with same score
			while($mxk){unset($re[$i][$mxk]); $rf[]=$mxk; $mxk=in_array_k($re[$i],$max);}
			if(count($rf)==1)$winner=$rf[0];
			$rok[]=$rf[0];}}
	//build
	if(is_array($rok))foreach($rok as $k=>$v){$stot=0; $rt='';
		for($i=1;$i<=5;$i++){$ratio=$rd[$v][$i]; $stot+=$ratio;
			$css=$stot<0.5?'':' active';
			$rt.=span($ratio,'anscell'.$css);}
		if($v==$winner)$css='winner'; else $css='';
		$ret.=div(span(val($rb,$v),'anstit').span($k+1,'anscell').$rt,'anscnt '.$css);}
	//winner
	if($winner){
		$answ=Sql::read('answ','ballot_lead','v','where id="'.$id.'"');
		$rw=explode('|',$answ); $win=$rw[$winner-1];
		$ret.=div(lang('the winner is').' : '.$win,'tit');}
	return $ret;}*/

//Admin
/*static function menu(){
	$app=ses('app'); $dev=ses('dev');
	//$ra[]=array('','lk','/','home','home');
	//$login=Auth::logbt(1);
	//$login=App::open('login');
	$r=self::com();
	if(!$r)$r=applist::comdir();
	//$r=array_merge($ra,$r);
	//$r[]=array('','t','','-',$login);
	//if(!class_exists($app))return $r;
	//$r[]=array('','lk','/app/'.$app,'',$app);
	if(auth(4) && $app)$r[]=array('','j','pagup|dev,seeCode|appSee='.$app,'code','Code');
	if($app && method_exists($app,'admin')){$rb=$app::admin(); if($rb)$r=array_merge($r,$rb);}
	return $r;}*/

/*static function menu0(){
	$app=ses('app'); $dev=ses('dev');
	$r[]=array('','lk','/','home','home');
	$r[]=array('','lk','/app/'.$app,'',$app);
	$rb=self::com();
	if(!$rb)$r=applist::comdir();
	if($rb)$r=array_merge($r,$rb);
	if(ses('user'))$rb=self::profilemenu();
	else $rb=self::profilelogin();
	if($rb)$r=array_merge($r,$rb);
	if(auth(4) && $app)$r[]=array('','j','pagup|dev,seeCode|appSee='.$app,'code','Code');
	if($app && method_exists($app,'admin')){$rb=$app::admin(); if($rb)$r=array_merge($r,$rb);}
	return $r;}*/

//profile
/*static function minimal($p){
	$uid=val($p,'uid'); $usr=val($p,'usr'); $sm=val($p,'small'); $wait=val($p,'wait');
	$r=self::datas($usr);
	$ret=telex::followbt(['usr'=>$usr,'small'=>'small','wait'=>$wait]);
	$f=self::avatar_im($r['avatar'],'mini');
	$ret.=self::divim($f,'avatarsmall','');
	//$ret.=self::username($r);
	$usr=val($r,'pusr',ses('user')); $name=$r['pname'];
	if($r['privacy'])$name.=ico('lock',14,'grey');
	$ret.=span(href('/'.$usr,$name),'usrnam').' ';
	$ret.=span(href('/'.$usr,'@'.$usr),'grey');
	if($r['status'])$ret.=div($r['status'],'','','padding:20px 0');
	return div($ret,'pane');}*/

//tlex
/*if(!$own)//$login=login::com(['auth'=>2]);
$login=Menu::call(array('app'=>'tlxcall','method'=>'profilelogin'));
else $login=Menu::call(array('app'=>'tlxcall','method'=>'profilemenu'));
//nav
$nav=span($login,'right');
$nav.=href('/',pic('home'),'btn');
//$nav.=href('/','Tlex','microsys abbt');
//$nav.=self::loadtm('tm='.$usr,'Tlex','microsys abbt');
if(ses('uid')){
	$bt=langph('notifications').span('','nbntf','tlxntf');
	//$nav.=aj('tlxbck|telex,read|ntf=1|tlxntf',$bt,'btn abbt');
	$nav.=self::loadtm('ntf=1',$bt,'btn abbt');
	$bt=langph('messages').span('','nbntf','tlxmsg');
	$nav.=pagup('chat,com|headers=1',$bt,'btn abbt');
	$nav.=ajtime('telex,lablbt','',langph('labels'),'btn abbt');
}
if($own)$nav.=ajtime('telex,chanbt','usr='.$usr.',list='.ses('list'),langph('lists'),'btn abbt');
if(ses('uid'))$nav.=aj('pblshcnt|telex,searchbt',langph('search'),'btn abbt');*/

//Admin
//array('folder','j/lk/in/t','action','picto','text')
/*static function menus(){
	$ra[]=array('','lk','/','home','home');
	$login=Auth::logbt(1);
	//$login=App::open('login');
	$r=self::com();
	if(!$r)$r=self::comdir();
	$r=array_merge($ra,$r);
	$app=ses('app'); $dev=ses('dev');
	$r[]=array('','t','','-',$login);
	//if(!class_exists($app))return $r;
	//$r[]=array('','lk','/app/'.$app,'',$app);
	if(auth(4) && $app)$r[]=array('','j','pagup|Admin,seeCode|appSee='.$app,'code','Code');
	if($app && method_exists($app,'admin')){$rb=$app::admin(); if($rb)$r=array_merge($r,$rb);}
	if(auth(6)){
		$r[]=array($dev.'/dev','j','ses,,reload||k=dev,v=prog','dev','dev');
		$r[]=array($dev.'/dev','j','ses,,reload||k=dev,v=prod','prod','prod');
		$r[]=array($dev.'/dev','j','popup|update,loaddl','download','update');
		$r[]=array($dev.'/dev','j','popup|upsql','download','upsql');
		$r[]=array($dev.'/admin','j','popup|admin_lang',ics('language'),'lang');
		$r[]=array($dev.'/admin','j','popup|admin_icons',ics('pictos'),'pictos');
		$r[]=array($dev.'/admin','j','popup|admin_help',ics('help'),'helps');
		$r[]=array($dev.'/admin','j','popup|devnote','connectdevelop','devnote');
		$r[]=array($dev,'j','popup|dev2prod','cloud-upload','publish');
		$r[]=array('','t','','timer',chrono('load'));}
	elseif($dev=='prog')$r[]=array('','lk','/?app='.$app.'&dev=prod','prod','prod');
	return $r;}*/

//Build
/*static function table0($array,$csa='',$csb='',$keys=''){$i=0; $tr='';
	if(is_array($array))
	foreach($array as $k=>$v){$td=''; $i++;
		$cs=$i==1?$csa:$csb;
		$alterenateCss=$i%2?'r2':'r1';
		if($keys)$td.=tag('td',['class'=>$cs],$k);
		if(is_array($v))foreach($v as $ka=>$va)
			$td.=tag('td',['class'=>$cs],$va);
		else $td.=tag('td',['class'=>$cs],$v);
		if($td)$tr.=tag('tr',['id'=>$k,'valign'=>'top','class'=>$alterenateCss],$td);}
	return tag('table','',$tr);}*/

//tlex
/*case('article'): $t=Sql::read('tit','articles','v','where id='.$p);
	$ret.=pagup('article,call|id='.$p,ico('file-text-o',$sz).span($t),$css); //popwidth:550px,tlx=1,
	break;//old*/

//art
/*static function edtbt($p){$id=val($p,'id'); $o=val($p,'o');
	if($o)return btj(pic('save'),atj('editbt',$id),'btsav');
	else return btj(pic('edit'),atj('editbt',$id),'btsav');}*/

//telex
/*static function countwords($p){
$r=explode(' ',$p['txt']); $n=count($r)-1;
foreach($r as $v)if(substr($v,0,1)!='@')$n+=mb_strlen($v);
return $n;}*/

//utils//strcount1
	//ajaxCall('strcnt'+id+"|telex,countwords","txt="+t,"");

//utils.js

function GetSelectedText(){var d=(window.getSelection?window.getSelection():document.getSelection?document.getSelection():document.selection.createRange().text);
alert(d);}

function getrangepos(id){
var ob=getbyid('txt'+id);
var r={text:"",start:0,end:0,length:0};
if(ob.setSelectionRange){r.start=ob.selectionStart; r.end=ob.selectionEnd;
	r.text=(r.start!==r.end)?ob.value.substring(r.start,r.end):"";}
alert(r);}

/*function getrange(id){
var ob=getbyid('txt'+id);
if(document.selection){
	var range=document.selection.createRange();
	var stored_range=range.duplicate();
	stored_range.moveToElementText(ob);
	stored_range.setEndPoint('EndToEnd',range);
	ob.selectionStart=stored_range.text.length-range.text.length;
	ob.selectionEnd=element.selectionStart+range.text.length;}
alert(ob.selectionStart);}*/

/*//position dans le node
function getrange(evt){
//if(!evt)evt=window.event;
var srcText=null;
if(navigator.appName!='Microsoft Internet Explorer'){
	var t=document.getSelection(); alert(t);
	srcText=evt.target.innerHTML;
	findPos(srcText,t);}
else{
	srcText=evt.srcElement.innerHTML;
	var t=document.selection.createRange();
		if(document.selection.type=='Text' && t.text>''){
		document.selection.empty();
		findPos(srcText,t.text);}}}

function findPos(srcText,text){
	var spos=srcText.indexOf(text);
	var epos=spos+text.toString().length-1;
	alert(text+'Start Position '+spos+'\n End Position '+epos);}*/

function od(t){
while(t.substr(t.length-1,1)==' ')t=t.substr(0,t.length-1);
while(t.substr(0,1)==' ')t=t.substr(1);
if(t)window.location.href='http://www.google.com/search?hl=en&q='+escape(t);}

function display(id){
var ob=getbyid(id); //alert(document.getSelection);
//if(document.setSelectionRange)alert(document.selectionStart);
if(document.getSelection)var str=document.getSelection(); else 
if(document.selection && document.selection.createRange){//ie
var range=document.selection.createRange(); var str=range.text;}
alert(str);//.getRangeAt(0)
//alert(str.rangeCount);
}
//if(window.Event)document.captureEvents(Event.MOUSEUP);
//document.ondblclick=display;

function selectdiv(debut,fin,id,act){var ob=getbyid(id);
	if(document.selection){var range=document.body.createTextRange();
		range.moveToElementText(ob); range.select();
		var selStart=ob.selectionStart; var selEnd=ob.selectionEnd;}
	else if(window.getSelection){var range=document.createRange();
		range.selectNode(ob); window.getSelection().addRange(range);}}

function selectxt(input,start,end){
	if(input.setSelectionRange){input.focus();
		input.setSelectionRange(start,end);}
	else if(input.createTextRange){
		var range=input.createTextRange();
		range.collapse(true);
		range.moveEnd('character',end);
		range.moveStart('character',start);
		range.select();}}

/*function rangepos(e){
//var ob=getbyid('txt'+id);
var selStart, selEnd;
if (typeof window.getSelection != "undefined") {
    var range = window.getSelection().getRangeAt(0);
    var preCaretRange = range.cloneRange();
    preCaretRange.selectNodeContents(e);
    preCaretRange.setEnd(range.endContainer, range.endOffset);
    selStart = preCaretRange.toString().length;
    selEnd = selStart + range.toString().length;} 
else if (typeof document.selection != "undefined" && document.selection.type != "Control") {
    var textRange = document.selection.createRange();
    var preCaretTextRange = document.body.createTextRange();
    preCaretTextRange.moveToElementText(e);
    preCaretTextRange.setEndPoint("EndToEnd", textRange);
    selStart = preCaretTextRange.text.length;
    selEnd = selStart + textRange.text.length;} 
else selStart=0;
alert(selStart+'-'+selEnd);}*/



#1703

//admin_conn

/*static function equalize0($p){
	$r=Sql::read('conn,lang,def',self::$db,'kkv','');
	$rb=array_keys($r);
	foreach($rb as $k=>$v)
		if(!isset($r[$v][$p['lang']])){
			if($p['lang']!='en' && isset($r[$v]['en']))
				$rc['fr'.$p['lang']][$v]=['en',$p['lang'],$r[$v]['en']];
				//$voc=Yandex::com(['from'=>'en','to'=>$p['lang'],'txt'=>$r[$v]['en']],1);
			elseif($p['lang']!='fr' && isset($r[$v]['fr']))
				$rc['fr'.$p['lang']][$v]=['fr',$p['lang'],$r[$v]['fr']];
				//$voc=Yandex::com(['from'=>'fr','to'=>$p['lang'],'txt'=>$r[$v]['fr']],1);
			//else $voc='';
			}
	if($rc)foreach($rc as $k=>$v){$rd=''; $re='';
		foreach($v as $ka=>$va){$rd[]=$va[2]; $re[]=[$va[0],$va[1]];}
		$res=Yandex::com(['from'=>$v[0],'to'=>$v[1],'txt'=>implode('||',$rd)],1);
		//$res=implode('||',$rd).br();
		if($rd){
			$rf=explode('||',$res);
			foreach($rd as $k=>$v){
				//Sql::insert(self::$db,array($k,$rf[$k],$p['lang']));
				echo $v.'=>'.$rf[$k].br();
			}
		}
	}
}*/

//telex
///panefoot
/*if($usr==ses('user'))
	$ret.=toggle($pr.'|tlxcall,del|idv='.$idv.',did='.$id,icit('flash','delete','del'));
$ret.=toggle($pr.'|tlxcall,report|idv='.$idv.',id='.$id.',cusr='.$usr,icit('warning','report'));
$ret.=toggle($pr.'|tlxcall,translate|id='.$id,icit('exchange','translate'));*/

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