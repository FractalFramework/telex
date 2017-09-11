<?php
class tlex{
static $private=0;
static $width=590;
static $objects=0;
//static $db='tlex';
static $db='tlex';
static $title='Tlex';
static $description='Tlex is an objects social network';
static $image='http://tlex.fr/usr/tlex/logo/tlex.png';
static $usr='';

//install
static function install(){
Sql::create(self::$db,['uid'=>'int','txt'=>'var','lbl'=>'int','ib'=>'int','ko'=>'int']);
Sql::create('tlex_ab',['usr'=>'var','ab'=>'var','list'=>'var','wait'=>'int','block'=>'int'],1);
Sql::create('tlex_web',['url'=>'var','tit'=>'var','txt'=>'var','img'=>'var']);
Sql::create('tlex_lik',['luid'=>'int','lik'=>'int']);
Sql::create('tlex_rpt',['rpuid'=>'int','tlxid'=>'int']);
//typntf:1=quote,2=reply,3=like,4=subsc,5=chat,6=subsc-approve
Sql::create('tlex_ntf',['4usr'=>'var','byusr'=>'var','typntf'=>'int','txid'=>'var','state'=>'int']);
Sql::create('tlex_to',['toid'=>'int','to'=>'var']);
Sql::create('tlex_tag',['tgid'=>'int','tag'=>'var']);}

static function titles($p){
$d=val($p,'appMethod'); if(!$d or $d==1)return;
$r['content']='welcome';
$r['read']='tlex';
if(val($p,'ntf'))$r['read']='notifications';
$r['editor']='publish';
if($type=val($p,'type')=='bers')$r['subscrptn']='subscribers';
else $r['subscrptn']='subscriptions';
$r['objplayer']='object player';
if(isset($r[$d]))return $r[$d];}

#headers		
static function injectJs(){
if(ses('uid'))return '
var activelive=1; var nbnew=0; var reloadtime=10000;
setTimeout("tlexlive(0)",500);
setTimeout("tlexlive(1)",3600000);';}

static function headers(){
Head::add_prop('og:title',self::$title);
Head::add_prop('og:description',self::$description);
Head::add_prop('og:image',self::$image);
Head::add('csslink','/css/tlex.css');
Head::add('jslink','/js/tlex.js');
Head::add('jscode',self::injectJs());}

#ajax
static function refresh($p){$own=ses('user');
$p['count']=1; $recents=self::api($p); $w='where 4usr="'.$own.'" and state=1';
$notifs=Sql::read('count(id)','tlex_ntf','v',$w.' and typntf in (1,2,3)');//quote,reply,like
$subscr=Sql::read('count(id)','tlex_ntf','v',$w.' and typntf=4');//subscr
$approv=Sql::read('count(id)','tlex_ntf','v',$w.' and typntf=6');//approve
$chat=Sql::read('count(id)','tlex_ntf','v',$w.' and typntf=5');//chat
return $recents.'-'.$notifs.'-'.$subscr.'-'.$approv.'-'.$chat;}

#saves
static function savemetas($d){
$r=self::playmetas($d); $f=''; $txt='';
if(!$r){$r=Html::metas($d);
	if($r[1])$txt=html_entity_decode(str_replace(array('“','”'),'',$r[1]));
	if($r[2])$f=File::saveimg($r[2],'web','590','400'); else $f='';
	if($r[0])Sql::insert('tlex_web',[$d,$r[0],$txt,$f]);}
return $r;}

//build connectors
static function build_conn($d,$o=''){$ret='';
$d=clean_n($d);
$d=str_replace("\n",' (nl) ',$d);
$r=explode(' ',$d);
foreach($r as $v){
	if(substr($v,0,1)=='@'){$v=substr($v,1); $ret[]='['.$v.':@]'; $_POST['ntf'][$v]=1;}
	elseif(substr($v,0,1)=='#'){$ret[]='['.substr($v,1).':#]'; $_POST['tag'][substr($v,1)]=1;}
	elseif(is_img($v)){
		$f=File::saveimg($v,'tlx','590','400');
		$ret[]='['.($f?$f:$v).':img]';}
	elseif(substr($v,0,4)=='http'){
		$v=before($v,'?utm',1);
		$xt=extension($v);
		if($xt=='.mp3')return '['.$v.':audio]';
		elseif($xt=='.mp4')return '['.$v.':mp4]';
		elseif($xt=='.pdf')return '['.$v.':pdf]';
		//elseif($p=='http')return Conn::href($d,'btlk','',1);
		else $metas=self::savemetas($v);
		if(Video::provider($v))$ret[]='['.$v.':video]';
		elseif($metas)$ret[]='['.$v.':web]';
		else $ret[]='['.$v.':url]';}
	else $ret[]=$v;
	$conn=substr($v,0,1)=='['?1:0;
	if($conn && substr($v,-4)==':id]' && $id=substr($v,1,-4)){
		$usr=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$id);
		if($usr)$_POST['ntf-r'][$usr]=1;}//notify
	elseif($n=strrpos($v,':')){$cnn=substr($v,$n+1,-1); 
		if($cnn)$_POST['lbl']=$cnn=='art'?'article':$cnn;}}
if($ret)$d=implode(' ',$ret);
$d=str_replace(' (nl) ',"\n",$d);
$d=str_replace(':img]'."\n",':img]',$d);
return trim($d);}

static function save($p){$txt=val($p,$p['ids']); $_POST['ntf']='';
if($oAuth=val($p,'oAuth')){$ok=Sql::read('puid','profile','v','where oAuth="'.$oAuth.'"');
	if($ok)sez('uid',$ok); else return 'error';}
if($txt){$txt=self::build_conn($txt,1); $ib=val($p,'ibs',0);
		$lbl=val($p,'lbl',0); if(!$lbl && $lbl=post('lbl'))$_POST['lbl']='';
		if($lbl && !is_numeric($lbl))$lbl=Sql::read('id','labels','v','where ref="'.$lbl.'"');
	$id=Sql::insert(self::$db,array(ses('uid'),$txt,(int)$lbl,$ib,0));
	if(isset($_POST['ntf']))self::saventf($id,1,'ntf');
	if(isset($_POST['tag']))self::saventf($id,0,'tag');
	if(isset($_POST['ntf-r']))self::saventf($id,2,'ntf-r');}
if(val($p,'apicom'))return isset($id)?$id:'error';
return self::read($p);}

static function modif($p){$txt=val($p,$p['ids']); $id=val($p,'id');
$txt=self::build_conn($txt);
if($id && $txt)Sql::update(self::$db,'txt',$txt,$id);
return self::one($p);}

#editor	
static function realib($id){
$d=Sql::read('txt',self::$db,'v',$id);
if(strpos($d,':id]'))$id=segment($d,'[',':id]');
return $id;}

static function upload($rid){
return '<form id="upl" action="" method="POST" onchange="upload(\''.$rid.'\')">
<label class="uplabel btn"><input type="file" id="upfile" name="upfile" multiple />
'.ico('image').'</label></form>';}

static function publishbt($t,$v,$rid){
$ja='div,tlxbck,resetform|tlex,save|ids=tx'.$rid.'|tx'.$rid.'';
$prm['onclick']='closediv(\'tlxapps\'); closediv(\'lbcbk\'); cltg(); resizearea(\''.$rid.'\');';
$prm['id']='edtbt'.$rid; $prm['data-prmtm']='tm='.ses('user');
return aj($ja,langph('publish'),'btsav',$prm).hidden('tx'.$rid,'['.$v.']');}

//publish
static function editor($p){
$ib=val($p,'ib'); $idv=val($p,'idv'); $rid=randid('ids'); $to=val($p,'to');
$ret=''; $appsbt='';
if($qo=val($p,'quote'))$msg='['.self::realib($qo).':id]';
//elseif($to)$msg='@'.$to.' ';
elseif($id=val($p,'id'))$msg=Sql::read('txt',self::$db,'v',$id);
else $msg=val($p,'msg');
if($ib)$ret.=div(lang('in-reply to').' '.$to,'grey');
if($qo)$ret.=div(lang('repost'),'grey');
$js='strcount1(\''.$rid.'\',216); resizearea(\''.$rid.'\');';
$r=['class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'placeholder'=>lang('message')];
$ret.=div(tag('textarea',$r,$msg));//form
$count=span('216','btxt small','strcnt'.$rid).' ';
$ja='div,tlxbck,resetform|tlex,save|ibs='.$ib.',ids='.$rid.'|'.$rid.',lbl';
$prm['onclick']='closediv(\'tlxapps\'); closediv(\'lbcbk\'); cltg(); resizearea(\''.$rid.'\');';
$prm['id']='edtbt'.$rid; $prm['data-prmtm']='tm='.ses('user');//'current';//return to current prmtm
$sav=span($count.aj($ja,langph('publish'),'btsav',$prm),'right').' ';//save
if(!$ib && !$qo && !$to && !$id){$divid='edcnt';
	$tg=toggle('tlxapps|tlxcall,menuapps|rid='.$rid,langph('applications'),'btn');
	$tg.=self::upload($rid);
	$tg.=bubble('ascii,call|rid='.$rid,ico('smile-o'),'btn',1);//'&#128522;'
	//if(auth(4))$tg.=toggle('tlxapps|fontawesome,com|id='.$rid,ico('fort-awesome'),'btn');
	//$tg.=bubble('tlxcall,labels',langph('labels'),'btn',1);
	$tg.=span(span('','','lblxt'),'','lbcbk').hidden('lbl',0);
	$sav.=div($tg,'edtbt'.$rid);//embed used to close others
	$sav.=div('','','tlxapps','');//put editor here if use object mode
	$ret.=div($sav,'','edtsv');}//,'display:none;'
else{$ret.=div($sav,'','edtsv'); $divid='';}
	//$ret.=btj(ico('close'),'closediv(\'pn'.$idv.'\')');
$ret.=div('','clear');
return div($ret,'',$divid);}

static function redit($p){$id=val($p,'id'); $rid=randid('ids');
$msg=Sql::read('txt',self::$db,'v',$id);
$js='strcount1(\''.$rid.'\',216); resizearea(\''.$rid.'\');';
$r=['class'=>'area','id'=>$rid,'onkeyup'=>$js,'onmousedown'=>$js,'cols'=>44];
$ret=tag('textarea',$r,$msg);//form
$count=span(216-mb_strlen(html_entity_decode($msg)),'btxt small','strcnt'.$rid).' ';
$ja='tlx'.$id.',,x|tlex,modif|id='.$id.',ids='.$rid.'|'.$rid;
$ret.=span($count.aj($ja,langp('modif'),'btsav'),'right').' ';
$ret.=div('','clear');
return $ret;}

#players
static function playmetas($d){
return Sql::read('tit,txt,img','tlex_web','rw','where url="'.$d.'"');}

static function playlink($d){
$d=http($d); $r=self::playmetas($d); $t=domain($d);
$r=array('href'=>$d,'title'=>$r[1],'class'=>'btlk','target'=>'_blank');
return tag('a',$r,$t);}

static function playweb($d,$o=''){
$d=http($d); $r=self::playmetas($d); //$r=self::savemetas($d);
if($o){$p=Video::provider($d); if($p)$id=Video::extractid($d,$p);}
$dom=domain($d);
$t=$r[0]?$r[0]:$dom;
$lk=href($d,$t,'btlk',1);
if(!$r)return $lk;
if(substr($r[2],0,4)=='http'){$f=$r[2];
	if($r[2])$imx=@getimagesize($r[2]); else $imx[0]='x';
	if($imx[0]>590)$img=img($r[2],'590'); else $img='';}
elseif($r[2])$f=self::thumb($r[2],'full'); else $f='';
if($imx=@getimagesize($f)){
	if($imx[0]>590)$img=img('/'.self::thumb($r[2],'medium'));//img('/'.$f,'590');
	elseif($imx[0])$img=img('/'.self::thumb($r[2],'mini'),100,100,'artim');} else $img='';
if($dom=='1nfo.net')$apc='playphilum'; else $apc='playweb';
$j='tlex,objplayer|popwidth=580,obj='.$apc.',p1='.nohttp($d);
if($img && isset($id))$ban=pagup('Video,call|p='.$p.',id='.$id,$img,'');
elseif($img)$ban=imgup('img/full/'.$r[2],$img,''); else $ban=$img;
if($dom=='1nfo.net')$bt=' '.pagup($j,langp('read'),'grey').' '; else $bt='';
$url=href($d,pic('url').$dom,'grey');
$ret=div($t,'bold').div($r[1],'stxt').div($url.$bt).div('','clear');
$ret=div($ret,'pncxt');
return div($ban.$ret,'panec');}

static function playphilum($p){
$id=after($p,'/');
$f='http://1nfo.net/api/id:'.$id.',preview:3';
$d=File::get($f);
$r=json_decode($d,true);
$ret=div($r[$id]['title'],'tit').div(html_entity_decode($r[$id]['content']),'txt');
$ret.=href(http($p));
return $ret;}
	
static function playquote($id){
$r=self::api(['id'=>$id]);
if(!$r)return div(lang('telex_deleted'),'paneb');
$v=$r[0]; $v['idv']='qlx'.$id;
$ret=self::panehead($v,'popup');
$ret=tag('header',['class'=>''],$ret);
$ret.=Conn::load(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader','opt'=>'it2','ptag'=>'no']);
return div($ret,'paneb',$v['idv']);}

static function thumb($f,$dim){$dr='img/';
$fb='medium/'.$f; $med=is_file($dr.$fb);
if($dim=='mini' or $dim=='micro')$im='mini/'.$f;
elseif($dim=='medium')$im=$med?$fb:'full/'.$f; else $im='full/'.$f;
if(is_file($dr.$im) && filesize($dr.$im))return $dr.$im;}

static function playthumb($f,$dim,$o='',$c=''){
$sz=590; if($dim=='micro')$sz=64;
if(substr($f,0,4)=='http')$f=File::saveimg($f,'tlx',$sz);
if(!$fb=self::thumb($f,$dim))return;
if($o)return img('/'.$fb,$sz);
if($f)return imgup('img/full/'.$f,img('/'.$fb,$sz),$c);}

static function url($p,$o,$e=''){$t=$o?$o:domain($p);
//$pop=popup('tlex,objplayer|obj=playweb,p='.$p.',o='.$o,$t,'btlk');
return href($p,$t.' '.ico('external-link'),'',$e);}

static function objplayer($p){$func=$p['obj'];
return self::$func(val($p,'p1'),val($p,'p2'));}

//connectors
static function reader($d,$b=''){
list($p,$o,$c)=readconn($d);
if(is_img($d))return img($d,'','',$o);
switch($c){
	case('@'):return bubble('tlex,profile|usr='.$p,'@'.$p,'btlk',1); break;
	case('#'):return aj('pagup|tlex,search_txt|srch='.$p,'#'.$p,'btlk'); break;
	case('b'):return tag('strong',['class'=>$o],$p); break;
	case('i'):return tag('em',['class'=>$o],$p); break;
	case('q'):return tag('blockquote',['class'=>$o],$p); break;
	case('red'):return tag('red',['class'=>$o],$p); break;
	case('clr'):return span($p,'','','background-color:#'.$o.'; color:#'.invert_color($o,1).';'); break;
	case('list'):return Conn::mklist($p); break;
	case('id'):if(is_numeric($p)){$_POST['repost']=$p; return self::playquote($p);} break;
	case('url'):return self::url($p,$o,''); break;
	//case('link'):return self::playlink($p); break;
	case('img'):if(Conn::$one!=1)$ret=self::playthumb($p,'full'); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('web'):if(Conn::$one!=1)$ret=self::playweb($p); else $ret=self::playlink($p);
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('pdf'):return pagup('iframe,get|url='.nohttp($p),ico('file-pdf-o',24).domain($p),'btxt'); break;
	case('video'):if(Conn::$one!=1)$ret=self::playweb($p,1); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('audio'):return audio($p); break;
	case('mp4'):return video($p); break;
	case('philum'):if(Conn::$one!=1)$ret=self::playphilum($p); else $ret='';
		self::$objects[$c][]=[$p,$o]; Conn::$one=1; return $ret; break;
	case('artit'):art::boot(); $tit=art::tit(['id'=>$p]); Conn::$one=1;
		return href('/art/'.$p,$tit,'btlk').' '; break;
	case('gps'):self::$objects[$c][]=[$p,$o]; $t=Gps::com(['coords'=>$p]);
		return pagup('map,call|coords='.$p,ico('map-marker',24).span($t)); break;
	case('app'):self::$objects[$c][]=[$p,$o]; Conn::$one=1;
		return aj('popup|'.$p.'|id='.$o,pic($p,24),'btn'); break;
	case('open'):if(method_exists($p,$o))return $p::$o; break;
	case('picto'):return picto($p,$o?$o:24); break;
	case('ico'):return ico($p,$o?$o:24); break;
	case('ascii'):return '&#'.$p.';'; break;
	default: if(method_exists($c,'call'))return self::display_app($c,$p,$o); break;}
return '['.$d.']';}

static function display_app($c,$p,$o){self::$objects[$c][]=[$p,$o];
	if(Conn::$one!=1 && $c=='art')$ret=art::preview(['id'=>$p]);
	//else $ret=App::open($c,['appMethod'=>'call','conn'=>'no','id'=>$p]);
	else{$q=new $c; $t=$o?$o:$q::tit(['id'=>$p]);
		if(Conn::$one!=1 && isset($q::$open) && $q::$open)
			$ret=App::open($c,['appMethod'=>'call','conn'=>'no','id'=>$p]);
		else{$bt=span(hlpic($c,28),'apptyp').' '.span($t,'apptit');
			$ret=div(pagup($c.',call|headers=1,id='.$p,$bt),'app');}}
	Conn::$one=1; return $ret;}

//objects
static function objects(){$ret=''; $r=self::$objects; $sz='24'; $css='licon';//36 from css
if($r)foreach($r as $kr=>$vr){$imok=0;
foreach($vr as $k=>$v){$fc=''; $ic=''; list($p,$o)=$v;
	if($kr=='img'){if($imok)$ret.=self::playthumb($p,'micro'); $imok=1;}
	if($ic)$t=ico($ic,$sz).span($t);//when called method need an interface
	if($fc)$ret.=pagup('tlex,objplayer|obj='.$fc.',p1='.$p.',p2='.$o,$t,$css);}}
if($ret)return div($ret,'');}//panec

#search	
static function search_txt($p){
$srch=val($p,'srch'); $ret='';
$r=self::api(['srh'=>$srch]);
if($r)foreach($r as $k=>$v)$ret.=div(self::pane($v,1),'pane','tlx'.$v['id']);
else $ret=help('no results','board');
return $ret;}

static function searchbt(){
$r=['type'=>'text','id'=>'srch','placeholder'=>lang('search'),'onkeypress'=>'SearchT(\'srch\')'];
return tag('form',['id'=>'srchfrm','name'=>'srchfrm','action'=>'javascript:Search(\'srch\');'],tag('input',$r,'',1)).div('','','cbksrch');}

#like
static function savelike($p){$id=val($p,'id'); $lid=val($p,'lid'); $nlik=val($p,'nlik');
if($lid){Sql::delete('tlex_lik',$lid); $p['lid']='';
$r=['4usr'=>$p['name'],'byusr'=>ses('user'),'typntf'=>3,'txid'=>$id];
$ex=Sql::read('id','tlex_ntf','v',$r); if($ex)Sql::delete('tlex_ntf',$ex);}
else{$p['lid']=Sql::insert('tlex_lik',[ses('uid'),$id]); tlxcall::saventf1($p['name'],$id,3);}
return self::likebt($p);}
	
static function likebt($p){$rid=randid('lik'); $mylik=''; $sty='';
$id=val($p,'id'); $lid=val($p,'lid'); $n=''; $nlik='';
if($lid){
	$nlik=Sql::read('count(id)','tlex_lik','v','where lik='.$id);
	$mylik=Sql::read('id','tlex_lik','v','where lik='.$id.' and luid='.ses('uid'));
	if($mylik)$sty='color:#e81c4f;';}
$bt=ico('heart',$sty,'like','','',span($nlik,'liknb'));
$ret=aj($rid.'|tlex,savelike|id='.$id.',lid='.$mylik.',name='.$p['name'],$bt);
return span($ret,'',$rid);}

#follow
static function followbt($p){$rid=val($p,'rid',randid('flw'));
$usr=val($p,'usr'); $sm=val($p,'small'); //$wait=val($p,'wait');//vcu
$w='where usr="'.ses('user').'" and ab="'.$usr.'"';
$id=Sql::read('id','tlex_ab','v',$w);
$rb=Sql::read('wait,block','tlex_ab','ra',$w);//contexts:user see visitor (ucv),
if($id){
	if($rb['wait'])$flag='pending'; elseif($rb['block'])$flag='blocked'; else $flag='unfollow';
	$bt=$sm?pic($flag):langph($flag);
	$ret=bubble('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,pic('menu'),'',1);
	$ret.=aj($rid.'|tlxcall,follow|usr='.$usr.',unfollow='.$id.',rid='.$rid,$bt,'btdel');}
else{$bt=$sm?pic('follow'):langph('follow');
	$ret=bubble('tlxcall,follow|chan=1,usr='.$usr.',rid='.$rid,$bt,'btsav',1);}
$c=val($p,'rid')?'':'followbt';
return span($ret,$c,$rid);}

#subscriptions
static function subscribt($usr,$uid,$role){
if(!$uid)$uid=Sql::read('id','login','v','where name="'.$usr.'"');
$n0=Sql::read('count(id)',self::$db,'v','where uid="'.$uid.'"');
$n1=Sql::read('count(id)','tlex_ab','v','where usr="'.$usr.'"');
$n2=Sql::read('count(id)','tlex_ab','v','where ab="'.$usr.'"');
$bt=div(lang('published telex'),'subscrxt').div($n0,'subscrnb clr');
$ret=div(self::loadtm('tm='.$usr.',noab=1',$bt,''),'subscrbt');
$bt=div(lang('subscriptions'),'subscrxt').div(span($n1,'','tlxabs'),'subscrnb clr');//ab
$ret.=div(aj('tlxbck|tlxcall,subscrptn|type=ption,usr='.$usr.'|tlxabs',$bt),'subscrbt');
$t=$role?'members':'subscribers';
$bt=div(lang($t),'subscrxt').div(span($n2,'','tlxsub'),'subscrnb clr');//sub
$ret.=div(aj('tlxbck|tlxcall,subscrptn|type=ber,usr='.$usr.'|tlxsub',$bt),'subscrbt');
//$n3=Sql::read('count(id)','tlex_ab','v','where ab="'.$usr.'"');//likes
//$bt=div(lang('likes'),'subscrxt').div($n3,'subscrnb clr');
//$ret.=div(self::loadtm('lik='.$usr.',noab=1',$bt,''),'subscrbt');
$ret.=hidden('tlxsubnb',$n2).hidden('tlxabsnb',$n1).div('','clear');
return div($ret,'subscrstats').div('','clear');}

#profile	
static function profile($p){$ret='';
$usr=val($p,'usr'); $uid=val($p,'uid'); $sm=val($p,'small');
if($usr){$rp=profile::build($p);
	$usn=div($rp['username'].$rp['status']);
	//$subsc=div($rp['follow'],'');
	if(ses('user')!=$usr)$subsc=div($rp['follow'],'right'); else $subsc='';
	$ret=div($rp['banner'].$subsc.$rp['avatar'].$usn,'','prfl');
	if(ses('user')==$usr && !$sm)$ret.=div($rp['subscribe'],'subscrban');}
if($ret)return div($ret,'profile');}

static function avatar($p){$im=val($p,'avatar');
$clr=sesif('clr'.val($p,'name'),Clr::random());
$f=profile::avatar_im($im,'mini',$clr);
return profile::divim($f,'avatarsmall',$clr);}

#notifications
static function saventf($id,$type,$o){
$r=isset($_POST[$o])?$_POST[$o]:'';
if($r)foreach($r as $k=>$v)if($k!=ses('user')){
	if($type)tlxcall::saventf1($k,$id,$type);
	if($o=='ntf')Sql::insertif('tlex_to',[$id,$k]);
	if($o=='tag')Sql::insertif('tlex_tag',[$id,$k]);}
$_POST[$o]='';}

static function readntf($v){$n=$v['typntf']; $by='@'.$v['byusr']; $ret='';
//$uname=Sql::read('name','login','v','where usr="'.$v['byusr'].'"');
if($v['state']==1)Sql::update('tlex_ntf','state','0',$v['ntid']);
if($n==1 && $v['ib'])$ret=$by.' '.lang('has_reply',1); 
elseif($n==1)$ret=$by.' '.lang('has_sent',1);
elseif($n==2)$ret=$by.' '.lang('has_repost',1);
elseif($n==3)$ret=$by.' '.lang('has_liked',1);
return div($ret,'ntftit');}

#channels
static function chanread($usr){
return Sql::read('distinct(list)','tlex_ab','rv','where usr="'.$usr.'" and wait=0 and block=0');}
static function chanbt(){$ret=self::loadtm('tm='.ses('user'),lang('all'));
//$r=sesclass('tlex','chanread',ses('user'));//todo reactive after subscr
$r=self::chanread(ses('user'));
if($r)foreach($r as $v)$ret.=self::loadtm('tm='.ses('user').',list='.$v,$v);
return div($ret,'list');}

#labels
static function lablbt(){$ret='';//self::loadtm('labl=',lang('all'));
	$r=Sql::read('ref,labels.id,icon','labels','kvv','inner join '.self::$db.' on lbl=labels.id
	inner join profile on puid=uid where privacy=0 or (puid="'.ses('uid').'" and privacy=1)');
	if($r)foreach($r as $k=>$v)$ret.=self::loadtm('labl='.$v[0],ico($v[1]).lang($k));
	return div($ret,'list');}

static function pub($p){$usr=val($p,'usr');
//$mail=Sql::read('mail','login','v','where name="'.$usr.'"');/
$r=Sql::read_inner('name','profile','login','puid','rv','where mail="'.ses('mail').'" and name!="'.$usr.'" and auth>1 and privacy=0');
if($r){foreach($r as $v)$ret[]=profile::small(['usr'=>$v]);
	return implode('',$ret);}}

#desktop
static function desktop($p){$css=val($p,'mode','licon');
//$bt=href('/desktop/'.ses('user'),ico('link'),'btxt');
$ret=aj('popup|desktop|dir=/documents',lang('desktop'),'btit');
$r=Sql::read('id,dir,type,com,picto,bt,auth','desktop','id','where uid="'.ses('uid').'" and auth<="'.ses('auth').'" and dir like "/documents%" order by id desc limit 10');
if($r)foreach($r as $k=>$v){
	if($v[1]=='img'){$f='img/full/'.$v[2];
		if(is_file($f))$ret.=imgup($f,self::playthumb($v[2],'micro',1).span($v[4]),$css);}
	elseif($v[1]=='pop')$ret.=popup($v[2].',headers=1',ico($v[3],24).span($v[4]),$css);
	elseif($v[1]=='pag')$ret.=pagup(''.$v[2].',headers=1',ico($v[3],24).span($v[4]),$css);
	elseif($v[1]=='lk')$ret.=href('/app'.$v[2],'',$css,1);
	else $ret.=aj($v[2],ico($v[3],24).span($v[4]),$css);}
return $ret.div('','clear');}

#read
static function relativetime($sec){$time=ses('time')-$sec;
$ret=lang('there_was').' ';
if($time>864000)$ret=strftime('%a %d %b',$sec);
if($time>86400)$ret=strftime('%d %b',$sec);
elseif($time>3600)$ret.=floor($time/3600).'h ';
elseif($time>60)$ret.=floor($time/60).'min ';
else $ret.=$time.'s';
return span($ret,'small');}

//thread
static function thread_parents($id,$ret=''){
$ib=Sql::read('ib',self::$db,'v','where '.self::$db.'.id="'.$id.'"',0);
if($ib){$ret[$ib]=1; $ret=self::thread_parents($ib,$ret);}
return $ret;}
static function thread_childs($id){
return Sql::read('id',self::$db,'k','where ib='.$id,0);}
static function sql_thread($id){
$ids=self::thread_childs($id);
$ids=self::thread_parents($id,$ids);
$ids[$id]=1; ksort($ids);
if($ids)$r=array_keys($ids);
if(isset($r))return 'where ('.self::$db.'.id='.implode(' or '.self::$db.'.id=',$r).')';}

//pane
static function panehead($v,$tg){$id=$v['id']; $idv=$v['idv']; $usr=$v['name'];
$name=bubble('tlex,profile|usr='.$usr.',small=1',$v['pname'],'btxt bold',1);
$usrnm=href('/'.$usr,'@'.$usr,'grey').' ';
$time=self::relativetime($v['now']).' ';
$url=href('/'.$id,ico('external-link',12),'grey');
$date=pagup('tlex,read|popwidth=600,th='.$id,$time,'grey');
$ico=$v['icon']?ico($v['icon']):'';
if($v['ref'])$label=span(span($ico.lang($v['ref']),'tx'),'label'); else $label='';
if($v['ib']){
	$to=Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id='.$v['ib']);
	$url.=' '.pagup('tlex,read|popwidth=600,th='.$id,lang('in-reply to',1).' '.$to,'grey small').' ';}
if($nb=Sql::read('count(id)',self::$db,'v','where ib='.$id))
	$url.=pagup('tlex,read|popwidth=600,th='.$id,$nb.' '.lang($nb>1?'replies':'reply',1),'grey small');
return div($name.' '.$usrnm.' - '.$date.' '.$url.' '.$label,'username');}

static function panefoot($v,$tg){
$id=$v['id']; $idv=$v['idv']; $pr='pn'.$idv; $usr=$v['name']; $ret='';
$ret.=toggle($pr.'|tlex,editor|idv='.$idv.',to='.$v['name'].',ib='.$id,icit('reply','to reply'));
$ret.=toggle($pr.'|tlex,editor|idv='.$idv.',quote='.$id,icit('retweet','quote'));
$ret.=self::likebt($v);
$ret.=toggle($pr.'|tlxcall,share|id='.$id,icit('share-alt','share'));
if(self::$objects)
	$ret.=toggle($pr.'|tlxcall,keep|idv='.$idv.',id='.$id,icit('download','keep'));
$ret.=toggle($pr.'|tlxcall,actions|id='.$id.',idv='.$idv.',uid='.$v['uid'].',usr='.$usr,icit('ellipsis-h','actions'));
return $ret;}

static function pane($v,$current=''){$id=$v['id']; $usr=$v['name'];
if($current){$v['idv']='tlx'.$id; $tg='popup';}//,,x
else{$v['idv']='tlx'.$id; $tg='popup';}
$avatar=bubble('tlex,profile|usr='.$usr.',small=1',self::avatar($v),'btxt',1);
$head=self::panehead($v,$tg);
self::$objects=''; $_POST['repost']=0;
if($v['ko']){$msg=div(help('telex_banned'),'alert'); self::$objects='';}
else{$msg=Conn::load(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader','ptag'=>1]);
$msg.=div(self::objects(),'objects');}
$msg=div($msg,'message');
/*if($id=$_POST['repost']){
	$by=bubble('tlex,profile|usr='.$usr,'@'.$usr,'',1);
	$head.=div($by.' '.lang('has_repost',1),'grey');}*/
if(ses('uid'))$foot=div(self::panefoot($v,$tg),'actions'); else $foot='';
$ret=div($avatar,'bloc_left').div($head.$msg.$foot,'bloc_content');
$ret.=div('','','pn'.$v['idv']);
if(isset($v['typntf']))$ret=self::readntf($v).$ret;
if($current==$id){self::$title='tlex.fr/'.$id.' by @'.$usr; self::$description=strip_tags($msg);
	self::$image='http://tlex.fr/img/mini/'.$v['avatar'];}
return $ret;}

#api
static function readusr($p,$usr){//authorized to watch
$open=Sql::read('auth','login','v','where name="'.$usr.'"');
if(!$open)return div(ico('lock').hlpxt('closed account'),'pane');
$prv=Sql::read('privacy','profile','v','where pusr="'.$usr.'"');
if($prv){$id=Sql::read('id','tlex_ab','v','where usr="'.ses('user').'" and ab="'.$usr.'" and wait=0 and block=0');
	if(!$id)return div(ico('lock').hlpxt('private account'),'pane');
	else return self::read($p);}
else return self::read($p);}

static function sql_timeline($usr,$from,$list,$noab,$since,$labl,$count){$sq='';
if(!$noab && !$labl){$sqa=$list?' and list="'.$list.'"':'';
	$r=Sql::read('ab','tlex_ab','rv','where usr="'.$usr.'"'.$sqa.' and wait=0 and block=0',0);
	if($r)$sq=' or name in ("'.implode('","',$r).'")';}
if($labl)$ret='where labels.id="'.$labl.'"';
elseif($list && !$noab)$ret='where ('.substr($sq,4).')';
elseif($usr==ses('user'))$ret='where (txt like "%@'.$usr.' %" or name="'.$usr.'"'.$sq.')';
else $ret='where (((txt like "%@'.$usr.' %" or name="'.$usr.'") and (privacy="0" or uid="'.ses('uid').'")))';//'.$sq.'
if($from)$from='and '.self::$db.'.id<'.$from.'';
elseif($since)$from='and '.self::$db.'.id>'.$since.'';
$limit=$count?'':'order by '.self::$db.'.id desc limit 20';//
$group='group by '.self::$db.'.id';
//$group='group by '.self::$db.'.id,uid,name,txt,lid,pname,avatar,clr,ref,icon,privacy,ko';//M5.7.5
return $ret.' '.$from.' '.$group.' '.$limit;}//and no!=1 

static function api($p){
$p=vals($p,['tm','th','id','ib','srh','ntf','from','list','noab','since','labl','count']);
if($p['count']){$cols='count('.self::$db.'.id)'; $vmode='v';}
else{$cols=self::$db.'.id,uid,name,txt,unix_timestamp('.self::$db.'.up) as now,ib,tlex_lik.id as lid,pname,avatar,clr,ref,icon,privacy,ko'; $vmode='rr';}
$inn='left join login on login.id=uid 
left join profile on puid=uid 
left join tlex_lik on '.self::$db.'.id=lik 
left join labels on lbl=labels.id ';
if($p['since'])$since=' and '.self::$db.'.id>'.$p['since']; else $since='';
$group=' group by tlex.id';
if($p['id'])$w='where '.self::$db.'.id='.$p['id'].$since.$group;
elseif($p['ib'])$w='where ib='.$p['ib'].$since.$group;
elseif($p['th'])$w=self::sql_thread($p['th']).$since.$group;
elseif($p['srh'])$w='where ((name="'.$p['srh'].'" or txt like "%'.$p['srh'].'%") and (privacy=0 or uid="'.ses('uid').'"))'.$since.$group.' order by id desc limit 20';
elseif($p['ntf']){$cols.=',tlex_ntf.id as ntid,byusr,typntf,state';
	$inn.='inner join tlex_ntf on txid='.self::$db.'.id ';
	$minid=$p['since']?' and '.self::$db.'.id>'.$p['since']:'';
	$limit=$p['count']?'':' order by '.self::$db.'.up desc limit 20';
	$w='where 4usr="'.ses('user').'"'.$minid.$limit;}
else $w=self::sql_timeline($p['tm'],$p['from'],$p['list'],$p['noab'],$p['since'],$p['labl'],$p['count']);
return Sql::read($cols,self::$db,$vmode,$inn.$w,0);}//1=verbose

static function read($p){$ret=''; $id=''; //$id will be in popup
$last=val($p,'from'); $th=val($p,'th'); $id=val($p,'id');
$rs=val($p,'rs'); $ib=val($p,'ib'); 
if($th && !$last)$id=$p['th']=$th;//thread
elseif($id && !$last)$p['id']=$id;//one
elseif($rs && !$last)$id=$p['rs']=$rs;//current+childs
elseif($ib && !$last)$p['ib']=$ib;//childs
else{$usr=val($p,'usr',ses('user')); $tm=val($p,'tm');//timeline
	$p['from']=$last; $p['tm']=$tm?$tm:$usr;}
if(isset($p))$r=self::api($p);
if(isset($r))foreach($r as $k=>$v){
	if($v['id']==$id)$css='pane hlight';
	elseif(isset($v['typntf']))$css=$v['state']==1?'pane hlight':'pane';
	else $css='pane';
	$pane=self::pane($v,$id);
	$ret.=div($pane,$css,'tlx'.$v['id'],'');}
//if(!$ret)if(isset($usr) && $usr==ses('user'))$ret=tlxcall::zero_telex();
return $ret;}

static function one($p){$r=self::api($p);
if($r)return self::pane(current($r),$p['id']);}

//http://tlex.fr/api.php?app=tlex&mth=call&prm=tm:dav
static function call($p){$r=self::api($p);
foreach($r as $k=>$v){self::$objects='';
	$r[$k]['avatar']='http://tlex.fr/img/full/'.$v['avatar'];
	$r[$k]['txt']=Conn::load(['msg'=>$v['txt'],'app'=>'tlex','mth'=>'reader']);}
	//$r[$k]['objects']=self::objects();
if($r)return json_r($r);}

//load button
static function loadtm($p,$t,$c='',$tg=''){
if($c)$r['class']=$c; if(!$tg)$tg='tlxbck';
$r['onclick']='refresh();';
$r['data-prmtm']=$p; $r['onmousedown']='ajbt(this)';
$r['data-j']='div,'.$tg.',,resetscroll|tlex,read|'.($p=='current'?'':$p);
return tag('a',$r,$t);}
	
static function vrfusr($d){return Sql::read('id','login','v','where name="'.$d.'" and auth>1');}
static function vrfid($d){return Sql::read_inner('name',self::$db,'login','uid','v','where '.self::$db.'.id="'.$d.'"');}

#admin
static function admin_bt($usr){$ret='';
if(ses('uid')){
	$bt=langph('notifications').span('','nbntf','tlxntf');
	//$nav.=aj('tlxbck|tlex,read|ntf=1|tlxntf',$bt,'btn abbt');
	//$ret=self::loadtm('ntf=1',$bt,'btn abbt');
	$ret=pagup('tlex,read|ntf=1',$bt,'btn abbt',1);
	$bt=langph('messages').span('','nbntf','tlxmsg');
	$ret.=pagup('chat,com|tlx=1,headers=1',$bt,'btn abbt');
	$ret.=ajtime('tlex,lablbt','',langph('labels'),'btn abbt');
	$ret.=ajtime('tlex,chanbt','usr='.$usr.',list='.ses('list'),langph('lists'),'btn abbt');
	$ret.=aj('edcnt|tlex,searchbt',langph('search'),'btn abbt');}
return $ret;}

#content
static function content($p){
//self::install();
//profile::install();
//if(ses('dev')=='prog')self::$db='tlex';//alternative table
$badusr=''; $badid=''; $dsk=''; $pub=''; $bigban=''; $publish=''; $refresh=''; $hub='';
$own=ses('user'); $desk=val($p,'desk'); $chat=val($p,'chat');
$ntf=val($p,'ntf'); $art=val($p,'art'); $usr=val($p,'usr'); $id=val($p,'id',val($p,'th'));
//okusr
if($usr){$okusr=self::vrfusr($usr); if(!$okusr){$badusr=1; $usr='';}}
//okid
if(is_numeric($usr)){$id=$usr; $usr='';}
if($id){$okid=self::vrfid($id); if(!$okid){$badid=1; $id='';} else{$p['id']=$id; $usr=$okid;}}
//art
if($art)$usr=Sql::read_inner('name','articles','login','uid','v',['articles.id'=>$art]);
if(!$usr && $own)$hub=$usr;
//profile
if($badid or $badusr)$profile=help('tlex','board');
elseif($usr && !$badusr)list($bigban,$profile)=profile::big(['usr'=>$usr]);
elseif(!$own or $usr or $id or $desk)$profile='';
else $profile=self::profile(['usr'=>$own,'face'=>'1']);
//dashboard
if(auth(1) && !$desk){//$dsk=self::pub(['usr'=>$usr]);
	if($own && !$usr)$dsk=div(self::desktop($p),'board','dsk');}
elseif(!$own && !$usr && !$id)//$pub=applist::tlex();
	$pub=div(help('tlex_donations','board').paypal::content(''),'credits');
$credits='';//div(help('credits','board-sans',1),'credits');
//publish
if($own && !$id && !$usr && !$badid && !$badusr && !$art && !$desk)
	$publish=self::editor('');
$publish=div($publish,'pblshcnt','pblshcnt');
//refreshbt
$bt=span('','nbntf','tlxrec').lang('new telex');
if($own && !$usr)$refresh=self::loadtm('current',$bt,'refreshbt');
elseif($own && $own!=$usr)$refresh=href('/'.$own,lang('back'),'refreshbt');
//stream
if($badid)$stream=help('404iderror');
elseif($badusr)$stream=help('404usrerror');
elseif($art)$stream=App::open('art',['id'=>$art,'appFrom'=>'tlex']);
elseif(!$usr && !$own && !$id)$stream=help('welcome','',1);
elseif($desk)$stream=Desk::load('desktop','com',val($p,'dir','/documents'));
elseif($chat)$stream=App::open('chat','');
elseif($usr && $usr!=$own)$stream=self::readusr($p,$usr);
else $stream=self::read($p);
$stream=div($stream,'','tlxbck');
//render
//$clr=ses('clr'.($usr?$usr:$own));
//Head::add('csscode','.clr,a.btxt:hover,a.btlk,a.btlk,.subscrnb,a:hover .subscrxt{color:#'.$clr.';} .btsav:hover{background-color:#'.$clr.';}');
if(get('popup'))$rs=['position:relative','margin-top:10px']; else $rs=['',''];
$ret=''; $hdash=''; $htime='';
if($bigban){$ret.=div($bigban,'bigprofile'); $hdash=' hdash'; $htime=' htime';}
$cnt=div($profile.$dsk.$credits.$pub,'dashboard'.$hdash,'',$rs[1]);
$cnt.=div($publish.$refresh.$stream,'timeline'.$htime,'wrapper',$rs[1]);
//prmtm
if(val($p,'th'))$pmtm='th='.$usr; elseif($ntf)$pmtm='ntf=1,usr='.$usr; 
elseif(!$id && !$desk && !$chat && !$art){// && $own
	if($usr)$pmtm='noab=1,tm='.$usr; else $pmtm='tm='.$own;}
else $pmtm='';
$cnt.=hidden('prmtm',$pmtm);
return $ret.div($cnt,'container');}
}
?>