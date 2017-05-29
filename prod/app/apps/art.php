<?php

class art{
static $private='0';
static $a='art';
static $db='articles';
static $cb='artwrp';
static $cols=['tit','txt','pub'];
static $typs=['var','var','int'];
static $title='Tlex';
static $description='Articles';
static $image='';

function __construct(){
	$r=['a','db','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(array_combine(self::$cols,self::$typs));}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function injectJs(){
	return '
function format(p,o){document.execCommand(p,false,o?o:null);}
function fontsize(n){var txt=document.getSelection(); alert(txt);}';}

static function headers(){
	Head::add_prop('og:title',self::$title);
	Head::add_prop('og:description',self::$description);
	Head::add_prop('og:image',self::$image);
	Head::add('csscode','
	.wrapper{width:100% - 40px); margin:0 0px;}
	.article{border:1px dotted white;}');
	Head::add('jscode',self::injectJs());}

//edit
static function wysiwyg($id){$ret=Build::wysiwyg($id);
	$ret.=self::editbt(['id'=>$id,'o'=>1]);
	return div($ret,'','edt'.$id,'display:none;');}

static function wswg($id){
	$ret=btj('[]',atjr('embed_slct',['[',']',$id]),'btn');
	$r=array('h','b','i','u','q','k','url','web');
	foreach($r as $k=>$v)$ret.=btj(lang($v,1),atjr('embed_slct',['[',':'.$v.']',$id]),'btn');
	return div($ret);}

static function gooduser($id){
	$ex=Sql::read('id',self::$db,'v','where id='.$id.' and (uid='.ses('uid').' or pub=1)');
	if($ex)return 1;}

//sav
static function del($p){$id=val($p,'id'); $ok=val($p,'ok');
	if(!self::gooduser($id))return lang('operation not permitted');
	if(!$ok)return aj('art'.$id.'|art,del|id='.$id.',ok=1',langp('confirm deleting'),'btdel');
	else{Sql::delete(self::$db,$id);
		Sql::delete('desktop','art,play|id='.$id,'com');}
	return self::stream($p);}

static function mkpub($p){
	Sql::update(self::$db,'pub',val($p,'pub'),val($p,'id'));
	return self::play($p);}

//save
static function untitled($p){$id=val($p,'id'); 
	$tit=val($p,'tit'.$id,lang('title'));
	$txt=val($p,'txt'.$id,lang('text'));
	$pub=ses('uid')?0:1;
	$savr=array('uid'=>ses('uid'),'tit'=>$tit,'txt'=>$txt,'pub'=>$pub);
	$id=Sql::insert(self::$db,$savr); $p['id']=$id;
	$com='art,play|id='.$id;//desk
	$nid=Sql::insert('desktop',[ses('uid'),'/documents/art','pag',$com,'file-o',$tit,2]);
	return $id;}

static function create($p){
	$r=['uid'=>ses('uid'),'tit'=>lang('title'),'txt'=>lang('text')];
	$id=Sql::read('id',self::$db,'v',$r);
	if($id)$p['id']=$id; else $p['id']=self::untitled($p);
	return self::call($p);}

static function savetxt($p){
	$id=val($p,'id'); $tit=val($p,'tit'.$id);
	$p['txt']=val($p,'txt'.$id,val($p,'txt-conn'.$id));
	if(!self::gooduser($id))return;
	if(val($p,'conn'))$txt=$p['txt']; else $txt=Trans::call($p);
	if($tit){$tit=trim(strip_tags(delbr($tit,' ')));
		if(strlen($tit)>144)$tit=substr($tit,0,144);
		Sql::update(self::$db,'tit',$tit,$id); return $tit;}
	if($txt)Sql::update(self::$db,'txt',trim($txt),$id,'id');
	if(val($p,'conn'))return self::play($p);
	return Conn::load(['msg'=>$txt,'ptag'=>1]);}

//edit
static function editconn($p){$id=val($p,'id'); $rid=val($p,'rid');
	if(!$id or !self::gooduser($id))return; $vid='txt-conn'.$id;
	list($tit,$txt)=Sql::read('tit,txt',self::$db,'rw',$id);
	//$ret=aj('art'.$id.'|art,play|conn=1,id='.$id,pic('back'),'btn').' ';
	$ret=aj('art'.$id.',,x|art,savetxt|conn=1,id='.$id.',rid='.$rid.'|'.$vid,langp('save'),'btsav').br();
	$ret.=self::wswg($vid);
	$ret.=textarea($vid,$txt,'64','28','','console').hidden('tit',$tit);
	return $ret;}

static function playconn($p){//from utils.js
	$ret=Sql::read('txt',self::$db,'v',$p['id']);
	return Conn::load(['msg'=>$ret,'mth'=>'minconn','ptag'=>1]);
	return nl2br(trim($ret));}

static function editbt($p){
	if($p['o'])return btj(langpi('save'),atj('editbt',$p['id']),'btsav');
	else return btj(langpi('edition'),atj('editbt',$p['id']),'btsav');}

static function privacy($p){$ret='';
	$id=val($p,'id'); $rid=val($p,'rid'); $pub=val($p,'pub'); $r=appx::privacy_prm();
	foreach($r as $k=>$v){$bt=$k==$pub?ico('check'):'';
		$ret.=aj('art'.$id.'|art,mkpub|id='.$id.',rid='.$rid.',pub='.$k,lang($v).$bt);}
	return div($ret,'list');}

static function edition($p){$ret=''; $usr=ses('user');
	$id=val($p,'id'); $rid=val($p,'rid'); $name=val($p,'name'); $pub=val($p,'pub');
	if(ses('user')===$name && $name)$own=1; else $own=0;
	if($rid)$ret=insertbt(langp('use'),$id.':art',$rid);
	if($own==1){
		$ret.=span(self::editbt(['id'=>$id,'o'=>0]),'','bt'.$id);
		$ret.=btj(langpi('restore'),atj('restore_art',$id),'btn');
		$ret.=aj('popup|art,editconn|id='.$id.',rid='.$rid.',edit=1',ico('edit'),'btn');
		$ret.=aj('art'.$id.'|art,del|id='.$id,langpi('delete'),'btdel');
		$ret.=bubble('art,privacy|id='.$id.',rid='.$rid.',pub='.$pub,langpi('privacy'),'btn',1);
		//$ret.=aj('popup|art',langpi('folder'),'btn');
		$ret.=href('/app/art',langpi('folder'),'btn');}
	$ret.=href('/app/art/'.$id,langpi('url'),'btn');
	return $ret;}

static function editable($p){
	$txt=val($p,'txt'); $txb=Trans::call($p);
	if(strlen($txt)==strlen($txb))return 1;}

//appx
static function edit($p){return self::call($p);}

//play
static function build($p){
	$id=val($p,'id'); $name=val($p,'name');
	$date=val($p,'date'); $pub=val($p,'pub');
	$title=val($p,'tit'); $txt=val($p,'txt');
	if(ses('user')==$name or $pub)$own=1; else $own=0;
	//if($own)$own=self::editable($p);
	$date=href('/art/'.$id,$date,'');
	$prmb=['id'=>'tit'.$id,'class'=>'editoff','contenteditable'=>'false'];
	if($own)$prmb['ondblclick']=atjr('editxt',['tit',$id]);
	if($own)$prmb['onblur']=atjr('savtxt',['tit',$id]);
	$ret['t']=tag('h1',$prmb,$title);
	$ret['mnu']=span(self::edition($p),'right');
	$by=lang('by').' '.href('http://tlex.fr/'.$name,$name,'btxt').' '; //if($pub or !$name)$by=appx::privacy($pub).' ';
	$ret['by']=span(tag('h4','',$by.$date),'small');
	$ret['edit']=self::wysiwyg($p['id']);
	//$ret['edit']=span(self::wswg($p['id']),'connbt','edt'.$id,'display:none;');
	$prm=['id'=>'txt'.$id,'class'=>'editoff','contenteditable'=>'false'];
	if($own)$prm['ondblclick']=atjr('editbt',[$id,1]);
	//if($own)$prm['onblur']=atj('editbt',$id);
	$txt=Conn::load(['msg'=>$txt,'ptag'=>1]);
	$rtx=tag('div',$prm,$txt);
	$ret['m']=div($rtx,'article');
	self::$title=$title;
	self::$description=substr(strip_tags($txt),0,100);
	return implode('',$ret);}

static function play($p){$id=val($p,'id');
	$cols='name,tit,txt,DATE_FORMAT('.self::$db.'.up,"%d/%m/%Y") as date,pub';
	if($id)$r=Sql::read_inner($cols,self::$db,'login','uid','ra','where '.self::$db.'.id='.$id);
	if(isset($r))$p=merge($p,$r);
	$apf=val($p,'appFrom');
	if($apf && $p['id']){$apf::$title=$r['tit'];//meta
		$apf::$description=substr(strip_tags($r['txt']),0,100); $apf::$image='';}
	$ret=self::build($p);
	return $ret;}

//stream
static function stream($p){
	return div(appx::stream($p),'board');}

//call
static function read($p){return self::play($p);}//old

static function preview($p){$id=val($p,'id');
	$r=Sql::read('tit,txt',self::$db,'rw',$id); if(!$r)return;
	$t=pagup('art,call|id='.$id,span(pic('art',32).' '.$r[0]),'btxt');
	$t.=href('/art/'.$id,pic('url'),'btxt');
	$txt=Conn::load(['msg'=>$r[1],'app'=>'Conn','mth'=>'noconn','ptag'=>'no']);
	$max=strlen($txt); if($max>140)$max=strpos($txt,'.',140); 
	if($max>240)$max=strpos($txt,' ',240);
	$txt=substr($txt,0,$max+1).'...';
	$ret=div($t,'bold').div($txt,'stxt').div('','clear');
	return div(div($ret,'pncxt'),'panec');}

static function txt($p){$id=val($p,'id');
	if($id)$txt=Sql::read('txt',self::$db,'v',$id);
	if($txt)return Conn::load(['msg'=>$txt,'ptag'=>1]);}

static function tit($p){$id=val($p,'id');
	if($id)return Sql::read('tit',self::$db,'v',$id);}

static function call($p){$id=val($p,'id'); $ret='';
	if($id){$p['id']=Sql::read('id',self::$db,'v',$id);
		if($p['id'])$ret=self::play($p); else $ret=help('article not exists','board');}
	return div($ret,'wrapper','art'.$id,'');}

static function com($p){
	return appx::com($p);}

#content
static function content($p){
	//self::install();
	$a=self::$a; $cb=self::$cb;
	$p['id']=val($p,'id',val($p,'param'));
	if($p['id'])$ret=$a::call($p);
	else $ret=$a::stream($p);
	return div($ret,'',$cb);}
}
?>
