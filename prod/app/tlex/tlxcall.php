<?php
class tlxcall{
static $private='0';

static function clr(){$d='mintcream thistle olivedrab lightyellow lightsteelblue lightblue lavender greenyellow darkseagreen darkkhaki cornflowerblue cadetblue blanchedalmond ThreeDLightShadow scrollbar';}

//menu apps
/*static function menuapps0($p){$ret=''; $rid=val($p,'rid');
$r=Sql::read('com','desktop','rv','where dir like "/apps/tlex/%" and auth<="'.ses('auth').'"');
$prm['onclick']='closebub(event);';
foreach($r as $k=>$v){$v=after($v,'/');
	if(method_exists($v,'com')){
	$bt=pic($v,28).div(hlpxt($v));
		$ret.=aj('tlxapps,,,1|'.$v.',com|headers=1,rid='.$rid,$bt,'cicon',$prm);}}
return $ret;}*/

static function menuapps($p){$ret=''; $rid=val($p,'rid');
$r=Sql::read('dir,com','desktop','kr','where dir like "/apps/tlex/%" and auth<="'.ses('auth').'"');
$prm['onclick']='closebub(event);';
foreach($r as $k=>$v){$ret.=div(after($k,'/'),'tit');
	foreach($v as $ka=>$va)if(method_exists($va,'com')){
		$prm['title']=addslashes(hlpxt($va.'_app'));
		$bt=pic($va,20).span(hlpxt($va));
		$ret.=aj('tlxapps,,,1|'.$va.',com|headers=1,rid='.$rid,$bt,'licon',$prm);}}
return div($ret,'tlxapps');}

//keep
static function keepsave($p){//dir,type,com,picto,bt
$id=val($p,'id'); $com=val($p,'com'); $d=val($p,'p1'); $ic='';
$o=val($p,'p2'); $t=val($p,val($p,'ict'),val($p,'tit'));
if($com=='img'){$ncom=$d; $t=before($d,'.'); $ic='image';}
elseif($com=='web'){$ncom='tlex,objplayer|obj=playweb,p1='.$d.',p2='.$o;}
elseif($com=='video'){$ncom='Video,call|p='.$d.',id='.$id; $ic='video';}
elseif($com=='art'){$ncom='art,call|id='.$d; $ic='file-o';}//dont'change it!
elseif($com=='chat'){$ncom='chat|id='.$d; $ic='comments';}
elseif($com=='gps'){$ncom='map,call|coords='.$d; $ic='map';}
elseif($com=='poll'){$ncom='poll,readtlx|id='.$d;}
elseif($com=='slide'){$ncom='slide,call|tid='.$d;}
elseif($com=='tabler'){$ncom='tabler,call|id='.$d;}
else{$ncom=$com.',call|id='.$d;}
$ic=$ic?$ic:$ic=ics($com); $ty=$com=='img'?$com:'pag';
$nid=Sql::insert('desktop',[ses('uid'),'/documents/'.$com,$ty,$ncom,$ic,$t,2]);
$bt=div(lang('added to desktop'),'valid').div(ico($ic).' '.$t,'tit');
return aj('dsk|tlex,desktop',$bt,'',['onclick'=>'Close(\'popup\')']);}

static function keep($p){
$id=val($p,'id'); $idv=val($p,'idv'); $com=val($p,'conn'); $ret=''; $ex=''; $txt='';
//if($conn)$ex=Sql::read('id','desktop','v','where id='.ses('uid').' and com="'.$com.'"');
//if($ex)return lang('already exists');
$dir=val($p,'dir'); $pic=val($p,'pic'); $bt=val($p,'bt'); $auth=val($p,'auth');
if(!$dir){
	if($id)$txt=Sql::read('txt',tlex::$db,'v',$id);
	tlex::$objects='';
	if($txt)$msg=Conn::load(['msg'=>$txt,'app'=>'tlex','mth'=>'reader']); $r=tlex::$objects;
	if($r)foreach($r as $kr=>$vr)foreach($vr as $k=>$v){$im=''; $pic=''; $t=''; list($p,$o)=$v;
	switch($kr){
	case('img'):$t=after($p,'/'); $im=Build::thumb($t,'micro'); break;
	case('video'):$rt=tlex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
		if(isset($rt[2]))$im=Build::thumb($rt[2],'micro'); $pic='youtube'; break;
	case('web'):$rt=tlex::playmetas($p); $t=isset($rt[0])?$rt[0]:nohttp($p);
		if(isset($rt[2]))$im=Build::thumb($rt[2],'micro'); $pic='newspaper-o'; break;
	case('chat'):$t=chat::tit(['id'=>$p]); $pic='comments'; break;
	case('art'):$t=art::tit(['id'=>$p]); $pic='file-text-o'; break;
	case('gps'):$t=Gps::com(['coords'=>$p]); $pic='map-marker'; break;
	//case('poll'):$t=poll::tit(['id'=>$p]); $pic=ics('poll'); break;
	//case('slide'):$t=slide::tit(['id'=>$p]); $pic=ics('slide'); break;
	default: $pic=ics($kr);
		if(method_exists($kr,'tit'))$t=$kr::tit(['id'=>$p]); else $t=$p; break;}
	if($pic)$pic=ico($pic,24); if($im)$im=img('/'.$im,45); $rid=randid('imk');
	$logo=($im?$im:$pic).' '.input($rid,$t,'40').' '; $bt=ico('save',24).' ';
	$bt=pagup('tlxcall,keepsave|com='.$kr.',p1='.nohttp($p).',p2='.$o.',ict='.$rid.',tit='.$t.'|'.$rid,$bt,'');
	$ret.=div($logo.$bt);}}
return div(lang('add2desktop'),'grey').div($ret,'bloc_content objects');}

//share
static function sendmail($p){$id=val($p,'id');
$ret=input('to','','20',lang('to'));
$ret.=hidden('subject',ses('user').' '.lang('send you',1).' '.lang('a',1).' '.lang('telex'));
$ret.=hidden('message','http://'.$_SERVER['HTTP_HOST'].'/'.$id);
$ret.=aj('sndml'.$id.'|sendmail,send|mode=text|subject,message,to',lang('send'),'btsav');
return $ret;}

static function twit_send($p){
$txt=val($p,'twitxt'); $twid=val($p,'twid'); $t=new Twit($twid);
$txt=html_entity_decode(utf8_decode($txt));
if($t)$q=$t->update($txt);
if(array_key_exists('errors',$q))$er=$q['errors'][0]['message'];
if(isset($er))return help('error','alert').$er;
return help('twit sent','valid');}

static function twit($p){$id=val($p,'id'); $twid=val($p,'twid');
$txt=Sql::read('txt',tlex::$db,'v',$id);
$txt=Conn::load(['msg'=>$txt,'app'=>'Conn','mth'=>'noconn','ptag'=>'no']);
$txt.="\n".'http://tlex.fr/'.$id;
$ret=div(textarea('twitxt',$txt,60,4,'','',140));
$ret.=aj('sndtw'.$id.'|tlxcall,twit_send|id='.$id.',twid='.$twid.'|twitxt',lang('tweet'),'btsav',['id'=>'edtbttwitxt']);
return $ret;}

static function share($p){$id=val($p,'id'); $root=host().'/'.$id;
$txt=Sql::read('txt',tlex::$db,'v',$id); tlex::$objects='';
$txt=Conn::load(['msg'=>$txt,'app'=>'Conn','mth'=>'noconn','ptag'=>0]);
$txt=(utf8_encode(strip_tags($txt)));
//$obj=tlex::objects(); if($obj)$txt.=trim(strip_tags($obj));
$tw='http://twitter.com/intent/tweet?original_referer='.$root.'&url='.$root.'&text='.utf8_encode($txt).' #tlex'.'&title=Tlex:'.$id; $fb='http://www.facebook.com/sharer.php?u='.$root;
$gp='https://plusone.google.com/_/+1/confirm?hl=fr-FR&url='.$root;
$st='http://wd.sharethis.com/api/sharer.php?destination=stumbleupon&url='.$root;
$ptw=ico('twitter-square','24','twitter'); $pfb=ico('facebook-official','24','facebook'); 
$pgp=ico('google-plus-circle','24','gplus'); $pst=ico('stumbleupon-circle','24','stumble');
$ret=href($tw,$ptw,'',1).href($fb,$pfb,'',1);
$ret.=href($gp,$pgp,'',1).href($st,$pst,'',1);
$ret.=popup('iframe,getcode|url=tlex.fr/api/read/'.$id,ico('code',24));
$ret.=aj('sndml'.$id.'|tlxcall,sendmail|id='.$id,ico('envelope-o',24)).span('','','sndml'.$id);
$r=Sql::read('id,owner','twitter','kv',['uid'=>ses('uid')]);
if($r){foreach($r as $k=>$v)
	$twapi[]=aj('sndtw'.$id.'|tlxcall,twit|id='.$id.',twid='.$k,ico('twitter',24).$v);
	$ret.=' '.span(implode('',$twapi),'','sndtw'.$id);}
return $ret;}

//del
static function del($p){$id=val($p,'did');
$uid=Sql::read('uid',tlex::$db,'v',$id);
if($uid!=ses('uid'))return lang('operation not permitted');
if(!val($p,'confirm')){
	$ja='tlxbck|tlxcall,del|did='.$id.',confirm=1';
	return aj($ja,langp('confirm deleting').' telex #'.$id,'btdel');}
else{Sql::delete(tlex::$db,$id); Sql::delete('tlex_ntf',$id,'txid');}
return tlex::read($p);}

//report
static function report($p){$id=val($p,'id'); //$idv=val($p,'idv');
$uid=ses('uid'); $usr=val($p,'cusr'); $idp=''; $nb=0;
if($uid && $id)$idp=Sql::read('id','tlex_rpt','v','where rpuid='.$uid.' and tlxid='.$id);
$nb=Sql::read('count(id)','tlex_rpt','v','where tlxid='.$id);//nb reports
$max=Sql::read('count(ab)','tlex_ab','v','where usr="'.$usr.'"');//nb ab
$prm='id='.$id.',cusr='.$usr;//.',idv='.$idv
if(val($p,'cancel')){Sql::delete('tlex_rpt',$idp);
	//echo $nb.'<='.ceil($max/20);
	if($max)if($nb<=ceil($max/20))Sql::update(tlex::$db,'no','0',$id);}
elseif($idp){$and='';//already
	if($nb>1)$and=', '.lang('and',1).' '.$nb.' '.lang('others',1);
	$prb=['data-prmtm'=>'id='.$id];
	$ccl=aj('tlxbck|tlxcall,report|'.$prm.',cancel=1',langp('cancel'),'btxt',$prb);
	return div(help('telex_reported').$and.' '.$ccl,'alert');}
elseif(val($p,'confirm')){Sql::insert('tlex_rpt',[$uid,$id]); $nb+=1;
	//echo $nb.'>='.ceil($max/20);
	if($max)if($nb>=ceil($max/20))Sql::update(tlex::$db,'no','1',$id);}
else{$ja='tlxbck|tlxcall,report|'.$prm.',confirm=1'; $prb=['data-prmtm'=>'id='.$id];
	$ret=lang('telex_max_reports').' : '.$nb.'/'.ceil($max/20);
	return aj($ja,langp('confirm reporting'),'btdel',$prb).' '.span($ret,'alert');}
return tlex::read($p);}

//translate
static function translate($p){$id=val($p,'id');
$txt=Sql::read('txt',tlex::$db,'v',$id);
$txt=Conn::load(['msg'=>$txt,'app'=>'Conn','mth'=>'noconn','ptag'=>0]);
return Yandex::read(['txt'=>$txt]);}

#actions
static function actions($p){
$id=$p['id']; $uid=$p['uid']; $idv=$p['idv']; $pr='pn'.$idv; $usr=$p['usr']; $ret='';
if($usr==ses('user'))
	$ret.=aj($pr.'|tlxcall,del|idv='.$idv.',did='.$id,langp('delete'));
if($usr==ses('user') or auth(6))$ret.=aj($pr.'|tlex,redit|id='.$id,langp('edit'));
else $ret.=aj($pr.'|tlxcall,report|idv='.$idv.',id='.$id.',cusr='.$usr,langp('report'));
$ret.=aj($pr.'|tlxcall,translate|id='.$id,langp('translate'));
if($usr!=ses('user'))
	$ret.=aj($pr.'|chat,discussion|uid='.$uid,langp('private discussion'));
return div($ret,'actions');}

//labels		
static function labels_in($p){$id=val($p,'lbl'); if(!$id)return;
list($ico,$ref)=Sql::read('icon,ref','labels','rw',$id);
return span(ico($ico).$ref,'','lblxt').hidden('lbl',$id);}

static function labels($p){$rid=val($p,'rid');
$call='lbcbk|tlxcall,labels_in|';
$ret=aj($call.'lbl=0',lang('none'));
$r=Sql::read('id,ref,icon','labels','','order by ref');
foreach($r as $k=>$v)$ret.=aj($call.'lbl='.$v[0],ico($v[2],'24').lang($v[1]));
return div($ret,'list');}

//ascii
static function ascii($p){
$id=val($p,'rid'); $all=val($p,'all'); $ret='';
$r=explode(' ',ascii::smileys());
foreach($r as $v)$ret.=btj('&#'.$v.';','insert(\'&#'.$v.';\',\''.$id.'\');','btn').' ';
return $ret;}

//notification (likes,follow)
static function saventf1($tousr,$id,$type){
$r=['4usr'=>$tousr,'byusr'=>ses('user'),'typntf'=>$type,'txid'=>$id];
$ex=Sql::read('id','tlex_ntf','v',$r);
if(!$ex){Sql::insert('tlex_ntf',[$tousr,ses('user'),$type,$id,'1']);
	$send=Sql::read('ntf','profile','v',['pusr'=>$tousr]);}
if($send!=1){
	$mail=Sql::read('mail','login','v',['name'=>$tousr]);
	$subject=lang('tlex');
	if($type==1)$hlp='notif_quote';
	if($type==2)$hlp='notif_reply';
	if($type==3)$hlp='notif_like';
	if($type==4)$hlp='notif_follow';
	if($type==5)$hlp='notif_chat';
	if($type==6)$hlp='notif_subscr';
	$url='http://'.$_SERVER['HTTP_HOST'].'/'.$id;
	$msg=ses('user').' '.hlpxt($hlp)."\n".$url;
	Mail::send($mail,$subject,$msg,'bot@tlex.fr','text');
}}

#subscrip-bers-tions
static function subscrptn($p){$type=val($p,'type'); $usr=val($p,'usr'); $ret='';
if($type=='ber')$r=Sql::read('usr','tlex_ab','k','where ab="'.$usr.'" order by up desc');
elseif($type=='ption')$r=Sql::read('ab','tlex_ab','k','where usr="'.$usr.'" order by up desc');
$n=isset($r)?count($r):'';
$tit=div($n.' '.langs('subscri'.$type,$n),'btit');
if($type=='ber'){//new subscr
	$rb=Sql::read('txid,id','tlex_ntf','kv','where 4usr="'.$usr.'" and typntf=4 and state=1');
	if($n=count($rb)){$newabs=implode(', ',array_keys($rb));
		$tit.=tag('h3','',$n.' '.langs('new subscriber',$n).': '.$newabs);}
	if($rb)foreach($rb as $k=>$v){
		//$ret.=tlex::profile(['usr'=>$k,'small'=>1]);
		$ret.=profile::standard(['usr'=>$k]);
		unset($r[$k]); Sql::update('tlex_ntf','state','0',$v);}
	//pending subscr
	$rc=Sql::read('usr','tlex_ab','k','where ab="'.$usr.'" and wait=1');
	if($n=count($rc)){
		$tit.=div($n.' '.langs('pending subscriber',$n),'alert').br();
		foreach($rc as $k=>$v){unset($r[$k]);
			$tit.=profile::standard(['usr'=>$k,'small'=>1,'approve'=>1]);}}}
if($type=='ption'){//approve subscr
	$rb=Sql::read('txid,id','tlex_ntf','kv','where 4usr="'.ses('user').'" and typntf=6 and state=1');
	if($n=count($rb)){
		foreach($rb as $k=>$v){
			$tit.=div($k.' '.lang('has approved',1),'valid').br();
			Sql::update('tlex_ntf','state','0',$v);}}}
if($r)foreach($r as $k=>$v){if(isset($rc[$k]))$wait=1; else $wait=0;
	//$ret.=tlex::profile(['usr'=>$k,'small'=>1,'wait'=>$wait]);
	$ret.=profile::standard(['usr'=>$k]);}
return $tit.div($ret,'');}

//follow
static function follow($p){
$usr=val($p,'usr'); $list=val($p,'subschan',val($p,'follow')); $rid=val($p,'rid');
if($list){//save
	$id=Sql::read('id','tlex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'"');
	if($id)Sql::update('tlex_ab','list',$list,$id);
	else{$private=Sql::read('privacy','profile','v','where pusr="'.$usr.'"');
		Sql::insert('tlex_ab',[ses('user'),$usr,$list,$private,0]);
		self::saventf1($usr,ses('user'),4);}
	return tlex::followbt($p);}
elseif($block=val($p,'block')){
	$id=Sql::read('id','tlex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'"');
	if($block==2)Sql::update('tlex_ab','block',0,$id);
	elseif($id)Sql::update('tlex_ab','block',1,$id);
	else Sql::insert('tlex_ab',[ses('user'),$usr,'','',1]);
	return tlex::followbt($p);}
elseif($apr=val($p,'refuse')){
	Sql::query('delete from tlex_ab where usr="'.$apr.'" and ab="'.ses('user').'"');
	return self::subscrptn(['usr'=>ses('user'),'type'=>'ber']);}
elseif($apr=val($p,'approve')){
	Sql::query('update tlex_ab set wait=0 where usr="'.$apr.'" and ab="'.ses('user').'"');
	self::saventf1($apr,ses('user'),6);
	return self::subscrptn(['usr'=>ses('user'),'type'=>'ber']);}
elseif($unf=val($p,'unfollow')){Sql::delete('tlex_ab',$unf);//unfollow
	$ntf=Sql::read('id','tlex_ntf','v','where 4usr="'.$usr.'" and typntf=4');
	Sql::delete('tlex_ntf',$ntf); return tlex::followbt($p);}
elseif(val($p,'chan')){//display
	$r=Sql::read('distinct(list)','tlex_ab','k','where usr="'.ses('user').'" and block=0');
	$act=Sql::read('list,block','tlex_ab','rw','where usr="'.ses('user').'" and ab="'.$usr.'"');
	$r=merge($r,['mainstream'=>1,'local'=>1,'global'=>1,'passion'=>1,'extra'=>1]);
	$ret=div(lang('subscribe_list'),'btit'); $bt='';
	$ret.=input('subschan','',18).' ';
	$ret.=aj($rid.'|tlxcall,follow|usr='.$usr.',rid='.$rid.'|subschan',lang('ok',1),'btsav');
	foreach($r as $k=>$v){$c=$k==$act[0]?'active':'';
		$bt.=aj($rid.'|tlxcall,follow|usr='.$usr.',rid='.$rid.',follow='.$k,$k,$c);}
	if($act[1])$bt.=aj($rid.'|tlxcall,follow|usr='.$usr.',rid='.$rid.',block=2',lang('blocked'),'active');
	else $bt.=aj($rid.'|tlxcall,follow|usr='.$usr.',rid='.$rid.',block=1',lang('block'),'del');
	return div($ret.div($bt,'list'),'pane',$rid,'width:240px;');}}

//new user
static function zero_telex(){
$ret=help('empty_home','board');
$ret.=div(aj('tlxbck|profile,edit',langp('edit profile')),'board');
return $ret;}

static function one($p){$r=tlex::api($p);
if($r)return self::pane(current($r),$p['id']);}

static function pane($v,$current=''){$id=$v['id']; $usr=$v['name'];
$v['idv']='tlx'.$id; $tg='popup';
$avatar='';//bubble('tlex,profile|usr='.$usr.',small=1',tlex::avatar($v),'btxt',1);
$head=tlex::panehead($v,$tg); tlex::$objects='';
if($v['ko'])$msg=div(help('telex_banned'),'alert');
else $msg=Conn::load(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader','ptag'=>1]);
$msg=div($msg,'message');//div($avatar,'bloc_left').
$ret=div($head.$msg,'bloc_content');
$ret.=div('','','pn'.$v['idv']);
return $ret;}

//apicom
//http://tlex.fr/api.php?app=tlxcall&mth=apicom&msg=hello&prm=oAuth:XXX
static function post($p){
$p['msg']=get('msg');
$p['lbl']=get('label');
$p['ids']='msg';
$p['apicom']=1;
$p['lbl']='';
$id=tlex::save($p);
if(is_numeric($id))$ret='http://tlex.fr/'.$id;
return $ret;}

}
?>
