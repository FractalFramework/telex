<?php
class voting{
static $private='0';
static $a='voting';
static $db='voting';
static $cb='vtn';
static $cols=['tit','txt'];
static $typs=['var','var'];
static $db2='voting_vals';
static $conn=0;
static $open=1;

function __construct(){
	$r=['a','db','db2','cb','cols'];
	foreach($r as $v)appx::$$v=self::$$v;}

static function install($p=''){
	appx::install(array_combine(self::$cols,self::$typs));
	Sql::create(self::$db2,['bid'=>'int','uid'=>'int','postcode'=>'int','candidate'=>'var','score'=>'int'],1);}

static function admin($rid=''){
	$p['rid']=$rid; $p['o']='1';
	return appx::admin($p);}

static function titles($p){return appx::titles($p);}
static function injectJs(){return '';}
static function headers(){
	Head::add('csscode','');
	Head::add('jscode',self::injectJs());}

#edit
static function collect($p){
	return appx::collect($p);}

static function del($p){
	return appx::del($p);}

static function save($p){
	return appx::save($p);}

/*static function save2($p){
	return appx::save2($p);}*/

static function modif($p){
	return appx::modif($p);}

static function form($p){
	return appx::form($p);}

static function save2($p){
	$db=self::$db2;
	$cols=Sql::columns($db,2);
	$r=vals($p,array_keys($cols));
	$p['id']=Sql::insert($db,$r);
	return aj(self::$cb.'|voting,play|id='.$p['bid'],langp('thank you'));}

static function form2($p){
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
	return $ret;}

static function edit($p){
	$p['collect']=self::$db2;
	return appx::edit($p);}

static function create($p){
	return appx::create($p);}

#build
static function build($p){
	return appx::build($p);}

static function template(){
	return '[[(tit)*class=tit:div][(txt)*class=txt:div]*class=paneb:div]';}

static function play($p){
	$ret=appx::play($p);
	$ret.=self::form2($p);
	return $ret;}

static function stream($p){
	return appx::stream($p);}

#call (read)
static function tit($p){
	return appx::tit($p);}

static function call($p){
	return appx::call($p);}

#com (edit)
static function com($p){
	return appx::com($p);}

#interface
static function content($p){
	//self::install();
	return appx::content($p);}
}
?>