<?php

class book{
static $private='0';
static $a='book';
static $db='book';
static $cb='bok';
static $cols=['tit'];
static $db2='book_chap';
static $conn=0;

function __construct(){
	$r=['a','db','cb','cols','db2'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install(){
	appx::install(['tit'=>'var']);
	Sql::create(self::$db2,['bid'=>'int','chapter'=>'var','txt'=>'text'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function injectJs(){
	return '
function format(p,o){document.execCommand(p,false,o?o:null);}';}

static function headers(){
	//Head::add_prop('og:title',self::$title);
	//Head::add_prop('og:description',self::$description);
	//Head::add_prop('og:image',self::$image);
	Head::add('csscode','
	.book{padding:20%; font-family:Times New Roman,serif; text-align:justify; font-size:20px;}
	.bookcover{margin:20px text-align:center;}
	.booknfo{padding:10px 0; text-align:center;}');
	Head::add('jscode',self::injectJs());}

static function save($p){return appx::save($p);}
static function modif($p){return appx::modif($p);}

//edit
static function del($p){
	$p['db2']=self::$db2;
	return appx::del($p);}

//save
static function form($p){
	//$p['html']='txt';
	return appx::form($p);}

static function create($p){
	return appx::create($p);}

static function subops($p){
	$id=val($p,'id'); $idb=val($p,'idb'); $op=val($p,'op');
	$cb=self::$cb; $a=self::$a; $db2=self::$db2;
	if($op=='add'){$cols=Sql::columns($db2,2);
		foreach($cols as $k=>$v)
			if($k=='bid')$rc[$k]=$id; elseif($k=='uid')$rc[$k]=ses('uid'); else $rc[$k]='';
		Sql::insert($db2,$rc);}
	elseif($op=='del')Sql::delete($db2,$idb);
	elseif($op=='sav'){$cols=Sql::columns($db2,6);
		$r=vals($p,$cols); Sql::updates($db2,$r,$idb);}
	return self::subform($p);}

static function subedit($p){
	$id=val($p,'id'); $idb=val($p,'idb');
	$a=self::$a; $cb=self::$cb; $j='id='.$id.',bid='.$id.',idb='.$idb;
	$r=Sql::read('chapter,txt',self::$db2,'ra',$idb);
	$ret=aj($cb.'edit|'.$a.',subops|'.$j,langp('back'),'btn');
	$ret.=aj($cb.'edit|'.$a.',subops|'.$j.',op=sav|chapter,txt',langp('save'),'btsav');
	$ret.=aj($cb.'edit|'.$a.',subops|'.$j.',op=del',langp('delete'),'btdel');
	$ret.=div(input('chapter',$r['chapter'],63,lang('chapter'),'',255));
	$ret.=divarea('txt',$r['txt']);
	return $ret;}

static function subform($p){$ret='';
	$id=val($p,'id');
	$a=self::$a; $cb=self::$cb; $db=self::$db; $db2=self::$db2;
	$cols=Sql::columns($db2,1);
	$r=Sql::read('id,'.$cols,$db2,'rr',['bid'=>$id]); //p($r);
	$ret.=tag('h3','',lang('chapters'));
	if($r)foreach($r as $k=>$v){$bt=ico('edit').' '.$v['chapter'];
		$ret.=aj($cb.'edit|'.$a.',subedit|id='.$id.',idb='.$v['id'],$bt,'licon');}
	$ret.=aj($cb.'sub|'.$a.',subops|op=add,id='.$id,langp('add'),'btn');
	return div($ret,'',$cb.'sub');}

//appx
static function edit($p){
	$p['sub']=1;
	return appx::edit($p);}

//play
static function build($p){$id=val($p,'id');
	$ra=Sql::read_inner('name,tit',self::$db,'login','uid','ra',$id);
	$rb=Sql::read('chapter,txt',self::$db2,'rr',['bid'=>$id]);
	return [$ra,$rb];}

static function play0($p){$a=self::$a;
	list($ra,$rb)=self::build($p);
	$ret=Vue::read($ra,'[[(tit):h1][(name)*class=btit:div]*class=booknfo:div]');
	$ret.=Vue::read_r($rb,'[[[(chapter):h3](txt)*class=txt:div]:div]');
	return div($ret,'book');}

static function cover($p){$id=val($p,'id');
	$r=Sql::read_inner('name,tit',self::$db,'login','uid','ra',$id);
	$rb=Sql::read('id,chapter',self::$db2,'kv',['bid'=>$id]);
	$ret=tag('h1','',$r['tit']);
	$ret.=div(href('/'.$r['name'],ico('user').$r['name']),'btit');
	foreach($rb as $k=>$v)$ret.=aj(self::$cb.$id.'|book,play|id='.$id.',chapter='.$k,$v,'licon');
	return div($ret,'');}

static function nav($p){
	$id=val($p,'id'); $idb=val($p,'chapter');
	$cb=self::$cb; $ret=''; $prev=''; $next='';
	$r=Sql::read('id',self::$db2,'rv','where bid='.$id.' order by id asc');
	foreach($r as $k=>$v)
		if($v==$idb){if(isset($r[$k-1]))$prev=$r[$k-1]; if(isset($r[$k+1]))$next=$r[$k+1];}
	if($prev)$ret.=aj($cb.$id.'|book,play|id='.$id.',chapter='.$prev,langp('previous'),'btn');
	if($next)$ret.=aj($cb.$id.'|book,play|id='.$id.',chapter='.$next,langp('next'),'btn');
	return $ret;}

static function reader($p){$id=val($p,'id'); $idb=val($p,'chapter'); $cb=self::$cb;
	$r=Sql::read('tit',self::$db,'ra',$id);
	$rb=Sql::read('chapter,txt',self::$db2,'ra',$idb);
	$ret=div(aj($cb.$id.'|book,call|id='.$id,$r['tit']),'btit booknfo');
	$tit=tag('h2','',$rb['chapter']);
	$ret.=div($tit.$rb['txt'],'btxt');
	$ret.=div(self::nav($p),'btit booknfo');
	return div($ret,'');}

static function play($p){$id=val($p,'id'); $idb=val($p,'chapter');
	if(!$idb)$ret=self::cover($p);
	else $ret=self::reader($p);
	return div($ret,'book');}

//stream
static function stream($p){
	return div(appx::stream($p),'');}

//call
static function txt($p){$id=val($p,'id');
	if($id)$txt=Sql::read('txt',self::$db,'v',$id);
	if($txt)return Conn::load(['msg'=>$txt,'ptag'=>1]);}

static function tit($p){$id=val($p,'id');
	if($id)return Sql::read('tit',self::$db,'v',$id);}

static function call($p){
	return div(self::play($p),'',self::$cb.$p['id']);}

static function com($p){
	return appx::com($p);}

#content
static function content($p){
	self::install();
	return appx::content($p);}
}
?>
